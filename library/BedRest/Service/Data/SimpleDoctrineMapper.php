<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace BedRest\Service\Data;

use BedRest\Service\Configuration;
use BedRest\Service\ServiceManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy as DoctrineProxy;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * SimpleEntityMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class SimpleDoctrineMapper implements DataMapper
{
    /**
     * Service configuration.
     * @var \BedRest\Service\Configuration
     */
    protected $configuration;

    /**
     * ServiceManager instance.
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * EntityManager instance.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     * Initialises the data mapper with the supplied options.
     * @param \BedRest\Service\Configuration  $configuration
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function __construct(Configuration $configuration = null, ServiceManager $serviceManager = null)
    {
        $this->configuration = $configuration;
        $this->serviceManager = $serviceManager;
    }

    /**
     * Sets the EntityManager instance.
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the EntityManager instance.
     * @throws \BedRest\Service\Data\Exception
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Maps an input array into a resource or set of resources.
     * @param  mixed                           $resource Resource to map data into.
     * @param  array                           $data     Array of data.
     * @throws \BedRest\Service\Data\Exception
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new Exception('Supplied data is not an array.');
        }

        // cast data
        $fields = $this->castFields($resource, $data);
        $associations = $this->castAssociations($resource, $data);

        $data = array_merge($fields, $associations);

        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }

    /**
     * Takes an input array of data and a resource, then proceeds to process each
     * property of the resource by finding data and casting it to the appropriate
     * format.
     * @param  object                          $resource
     * @param  array                           $data
     * @throws \BedRest\Service\Data\Exception
     * @return array
     */
    protected function castFields($resource, array $data)
    {
        // get the class meta data for the entity
        $em = $this->getEntityManager();

        $classMetaInfo = $em->getClassMetadata(get_class($resource));

        // process basic fields
        $castData = array();

        foreach ($classMetaInfo->fieldMappings as $fieldName => $fieldMapping) {
            // skip fields not included in the data
            if (!array_key_exists($fieldName, $data)) {
                continue;
            }

            // enter into the final cast data array
            $castData[$fieldName] = $this->castField($data[$fieldName], $fieldMapping);
        }

        return $castData;
    }

    /**
     * @param  mixed                           $value
     * @param  array                           $fieldMapping
     * @throws \BedRest\Service\Data\Exception
     * @return mixed
     */
    protected function castField($value, array $fieldMapping)
    {
        switch ($fieldMapping['type']) {
            case Type::INTEGER:
            case Type::BIGINT:
            case Type::SMALLINT:
                $value = (int) $value;
                break;
            case Type::BOOLEAN:
                $value = (bool) $value;
                break;
            case Type::DATE:
            case Type::DATETIME:
            case Type::DATETIMETZ:
            case Type::TIME:
                $value = $this->castDateField($value);
                break;
            case Type::DECIMAL:
            case Type::FLOAT:
                $value = (float) $value;
                break;
            case Type::STRING:
            case Type::TEXT:
                $value = (string) $value;
                break;
            case Type::TARRAY:
                $value = (array) $value;
                break;
            case Type::OBJECT:
                throw new Exception('"object" type mapping is not currently supported');
                break;
            case TYPE::BLOB:
                throw new Exception('"blob" type mapping is not currently supported');
                break;
            default:
                throw new Exception("Unknown type \"{$fieldMapping['type']}\"");
                break;
        }

        return $value;
    }

    /**
     * Casts Date, DateTime and Time fields, handling a variety of formats.
     * @param  mixed                           $value
     * @throws \BedRest\Service\Data\Exception
     * @return \DateTime
     */
    protected function castDateField($value)
    {
        if ($value instanceof \DateTime) {
            // do nothing
        } elseif (empty($value)) {
            // force empty values to NULL
            $value = null;
        } elseif (is_array($value)) {
            // interpret the value as an array-cast DateTime object
            if (!isset($value['date'])) {
                throw new Exception(
                    'Missing "date" component in array.'
                );
            }

            $dateString = $value['date'];
            if (isset($value['timezone'])) {
                $dateString .= ' ' . $value['timezone'];
            }

            $value = new \DateTime($dateString);
        } elseif (is_string($value)) {
            // treat it as a DateTime string
            $value = new \DateTime($value);
        } elseif (is_integer($value)) {
            // treat it as a UTC timestamp
            $value = \DateTime::createFromFormat('U', $value);
        }

        return $value;
    }

    /**
     * Casts raw association data into entities or collections using the Doctrine ClassMetadata object
     * for a particular entity.
     * @param  string                          $resource
     * @param  array                           $data
     * @return array
     * @throws \BedRest\Service\Data\Exception
     */
    protected function castAssociations($resource, array $data)
    {
        // get the class meta data for the entity
        $em = $this->getEntityManager();

        $classMetaInfo = $em->getClassMetadata(get_class($resource));

        // process all associations
        $output = array();

        foreach ($classMetaInfo->associationMappings as $name => $mapping) {
            // skip associations not included in the data
            if (!isset($data[$name])) {
                continue;
            }

            // detect the value of the association
            if ($classMetaInfo->isSingleValuedAssociation($name)) {
                $value = $this->castSingleValuedAssociation($data[$name], $mapping);
            } elseif ($classMetaInfo->isCollectionValuedAssociation($name)) {
                $value = $this->castCollectionValuedAssociation($data[$name], $mapping);
            } else {
                throw Exception::invalidAssociationType($classMetaInfo->getName(), $name);
            }

            $output[$name] = $value;
        }

        return $output;
    }

    /**
     * Casts raw association data for a single valued association (e.g. a to-one mapping).
     * @param  mixed  $data
     * @param  array  $mapping
     * @return object
     */
    protected function castSingleValuedAssociation($data, array $mapping)
    {
        $output = $this->getEntityManager()->find($mapping['targetEntity'], $data);

        return $output;
    }

    /**
     * Casts raw association data for a multi-valued association (e.g. a to-many mapping).
     * @param  mixed $data
     * @param  array $mapping
     * @return array
     */
    protected function castCollectionValuedAssociation($data, array $mapping)
    {
        $output = array();

        foreach ($data as $item) {
            $output[] = $this->getEntityManager()->find($mapping['targetEntity'], $item);
        }

        return $output;
    }

    /**
     * Reverse maps a data set into an array.
     * @param  mixed $data  Data to reverse map.
     * @param  mixed $depth Depth to reverse map associations.
     * @return array
     */
    public function reverse($data, $depth)
    {
        return $this->reverseItem($data, $depth, 0);
    }

    /**
     * Reverse maps a single item from a data set.
     * @param  mixed        $data
     * @param  integer      $depth
     * @param  integer      $currentDepth
     * @return array|object
     */
    protected function reverseItem($data, $depth, $currentDepth)
    {
        $return = null;

        if (is_array($data) || $data instanceof Collection) {
            $return = array();

            foreach ($data as $key => $value) {
                $return[$key] = $this->reverseItem($value, $depth, $currentDepth);
            }
        } elseif (is_object($data)) {
            $class = get_class($data);
            $parentClass = get_parent_class($data);

            $isProxy = ($data instanceof DoctrineProxy);

            if (!$this->getEntityManager()->getMetadataFactory()->isTransient($class) ||
                ($isProxy && !$this->getEntityManager()->getMetadataFactory()->isTransient($parentClass))
            ) {
                $currentDepth++;
                $return = $this->reverseEntity($data, $depth, $currentDepth);
            } elseif (method_exists($data, '__sleep')) {
                $return = $data->__sleep();
            }
        } else {
            $return = $data;
        }

        return $return;
    }

    /**
     * Reverse maps a single resource entity.
     * @param  object  $resource
     * @param  integer $maxDepth
     * @param  integer $currentDepth
     * @return array
     */
    protected function reverseEntity($resource, $maxDepth, $currentDepth)
    {
        if ($currentDepth > $maxDepth) {
            $output = array(
                'id' => $resource->id
            );
        } else {
            $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));

            $fieldData = $this->reverseEntityFields($resource, $classMetadata);
            $associationData = $this->reverseEntityAssociations($resource, $classMetadata, $maxDepth, $currentDepth);

            $output = array_merge($fieldData, $associationData);
        }

        return $output;
    }

    /**
     * Reverse maps entity fields using the class metadata to perform any casting.
     * @param  mixed                               $resource
     * @param  \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return array
     */
    protected function reverseEntityFields($resource, ClassMetadata $classMetadata)
    {
        $data = array();

        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            $value = $resource->$property;

            $data[$property] = $value;
        }

        return $data;
    }

    /**
     * Reverse maps entity associations, using the class metadata to determine those associations.
     * @param  mixed                               $resource
     * @param  \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @param  integer                             $maxDepth
     * @param  integer                             $currentDepth
     * @return array
     */
    protected function reverseEntityAssociations($resource, ClassMetadata $classMetadata, $maxDepth, $currentDepth)
    {
        $data = array();

        foreach ($classMetadata->associationMappings as $association => $mapping) {
            $value = $resource->$association;

            // force proxies to load data
            if ($value instanceof DoctrineProxy) {
                $value->__load();
            }

            if ($mapping['type'] & ClassMetadata::TO_ONE) {
                // single entity
                $data[$association] = $this->reverseItem($resource->$association, $maxDepth, $currentDepth);
            } else {
                // collections must be looped through, assume each item within is an entity
                $data[$association] = array();

                if ($value === null) {
                    $value = array();
                }

                foreach ($value as $item) {
                    $data[$association][] = $this->reverseItem($item, $maxDepth, $currentDepth);
                }
            }
        }

        return $data;
    }
}
