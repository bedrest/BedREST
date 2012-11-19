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
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;

/**
 * AbstractDoctrineMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
abstract class AbstractDoctrineMapper implements DataMapper
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
            if (!isset($data[$fieldName])) {
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
        } elseif (is_array($value)) {
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
            $value = new \DateTime($value);
        } elseif (is_integer($value)) {
            $value = \DateTime::createFromFormat('U', $value);
        }

        return $value;
    }

    /**
     * Maps data into a resource or set of resources.
     * @todo Make this method abstract once again. Can't have an abstract method implement an interface method, see
     *       https://bugs.php.net/bug.php?id=43200 for more information. PHP 5.3.9+ allows this.
     * @param mixed $resource Resource to map data into.
     * @param mixed $data     Data to be mapped.
     */
    public function map($resource, $data)
    {
    }

    /**
     * Reverse maps a resource into the desired format.
     * @todo Make this method abstract once again. Can't have an abstract method implement an interface method, see
     *       https://bugs.php.net/bug.php?id=43200 for more information. PHP 5.3.9+ allows this.
     * @param  mixed $resource Data to reverse map.
     * @param  mixed $depth    Depth to reverse map associations.
     * @return mixed
     */
    public function reverse($resource, $depth)
    {
    }
}
