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

namespace BedRest\Model\Doctrine;

use BedRest\Service\ServiceManager;
use BedRest\Service\Data\Mapper as MapperInterface;
use BedRest\Service\Data\Exception as DataException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Proxy as DoctrineProxy;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Mapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Mapper implements MapperInterface
{
    /**
     * ServiceManager instance.
     *
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * EntityManager instance.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     * Initialises the data mapper with the supplied options.
     *
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager = null)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Sets the EntityManager instance.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the EntityManager instance.
     *
     * @throws \BedRest\Service\Data\Exception
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Maps an input array into a resource or set of resources.
     *
     * @param mixed $resource Resource to map data into.
     * @param array $data     Array of data.
     *
     * @throws \BedRest\Service\Data\Exception
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new DataException('Supplied data is not an array.');
        }

        $em = $this->getEntityManager();
        $classMetaInfo = $em->getClassMetadata(get_class($resource));

        foreach ($classMetaInfo->fieldMappings as $name => $mapping) {
            if (!array_key_exists($name, $data)) {
                continue;
            }

            $resource->$name = $this->castField($data[$name], $mapping);
        }

        foreach ($classMetaInfo->associationMappings as $name => $mapping) {
            if (!array_key_exists($name, $data)) {
                continue;
            }

            $resource->$name = $this->castAssociation($data[$name], $mapping);
        }
    }

    /**
     * Casts an individual field value using the field mapping provided.
     *
     * @param mixed $value
     * @param array $fieldMapping
     *
     * @throws \BedRest\Service\Data\Exception
     *
     * @return mixed
     */
    protected function castField($value, array $fieldMapping)
    {
        if ($value === null) {
            return null;
        }

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
            default:
                throw new DataException("\"{$fieldMapping['type']}\" type mapping is not currently supported");
                break;
        }

        return $value;
    }

    /**
     * Casts Date, DateTime and Time fields, handling a variety of formats.
     *
     * @param mixed $value
     *
     * @throws \BedRest\Service\Data\Exception
     *
     * @return \DateTime
     */
    protected function castDateField($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        } elseif (is_integer($value)) {
            return \DateTime::createFromFormat('U', $value);
        }

        if (is_array($value)) {
            // interpret the value as an array-cast DateTime object
            if (!isset($value['date'])) {
                throw new DataException(
                    'Missing "date" component in array.'
                );
            }

            $dateString = $value['date'];
            if (isset($value['timezone'])) {
                $dateString .= ' ' . $value['timezone'];
            }

            $value = $dateString;
        }

        return new \DateTime($value);
    }

    /**
     * Casts an individual association using the association mapping provided.
     *
     * @param mixed $value
     * @param array $mapping
     *
     * @return array
     */
    protected function castAssociation($value, array $mapping)
    {
        $castValue = null;

        if ($mapping['type'] & ClassMetadata::TO_MANY) {
            $castValue = array();

            foreach ($value as $item) {
                $castValue[] = $this->getEntityManager()->find($mapping['targetEntity'], $item);
            }
        } else {
            $castValue = $this->getEntityManager()->find($mapping['targetEntity'], $value);
        }

        return $castValue;
    }

    /**
     * Reverse maps a data set into an array.
     *
     * @param mixed $data  Data to reverse map.
     * @param mixed $depth Depth to reverse map associations.
     *
     * @return array
     */
    public function reverse($data, $depth)
    {
        return $this->reverseItem($data, $depth, 0);
    }

    /**
     * Reverse maps a single item from a data set.
     *
     * @param mixed   $data
     * @param integer $depth
     * @param integer $currentDepth
     *
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
            // force proxies to load their data
            $isEntity = !$this->getEntityManager()->getMetadataFactory()->isTransient(get_class($data));
            $isProxy = ($data instanceof DoctrineProxy);

            if ($isProxy) {
                $data->__load();
            }

            if ($isEntity || $isProxy) {
                $return = $this->reverseEntity($data, $depth, ++$currentDepth);
            } elseif (method_exists($data, '__sleep')) {
                $return = $data->__sleep();
            } else {
                $return = (array) $data;
            }
        } else {
            $return = $data;
        }

        return $return;
    }

    /**
     * Reverse maps a single resource entity.
     *
     * @param object  $resource
     * @param integer $maxDepth
     * @param integer $currentDepth
     *
     * @return array
     */
    protected function reverseEntity($resource, $maxDepth, $currentDepth)
    {
        if ($currentDepth > $maxDepth) {
            return array(
                'id' => $resource->id
            );
        }

        $output = array();
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));

        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            $output[$property] = $resource->$property;
        }

        foreach ($classMetadata->associationMappings as $association => $mapping) {
            $value = $resource->$association;

            // ensure that collections are always returned as arrays, not NULL
            if ($mapping['type'] & ClassMetadata::TO_MANY) {
                if ($value === null) {
                    $value = array();
                }
            }

            $output[$association] = $this->reverseItem($value, $maxDepth, $currentDepth);
        }

        return $output;
    }
}
