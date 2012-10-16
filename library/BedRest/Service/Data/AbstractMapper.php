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

use BedRest\Rest\Configuration;
use BedRest\Service\ServiceManager;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;

/**
 * AbstractMapper
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
abstract class AbstractMapper implements DataMapper
{
    /**
     * Configuration.
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

    /**
     * Service manager.
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * Constructor.
     * Initialises the data mapper with the supplied options.
     * @param \BedRest\Rest\Configuration $configuration
     */
    public function __construct(Configuration $configuration = null, ServiceManager $serviceManager = null)
    {
        $this->configuration = $configuration;
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns the configuration.
     * @return \BedRest\Rest\Configuration
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Gets the entity manager.
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        if (!$this->configuration instanceof Configuration) {
            throw new Exception('Configuration not provided');
        }

        $em = $this->configuration->getEntityManager();

        if (!$em instanceof EntityManager) {
            throw new Exception('EntityManager not provided');
        }

        return $em;
    }

    /**
     * Takes an input array of data and a resource, then proceeds to process each
     * property of the resource by finding data and casting it to the appropriate
     * format.
     * @param object $resource
     * @param array $data
     * @return array
     * @throws \BedRest\Exception
     */
    protected function castFieldData($resource, array $data)
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

            // cast the data to the correct type
            $value = $data[$fieldName];

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
                    if ($value instanceof \DateTime) {
                        // do nothing
                    } elseif (is_array($value)) {
                        if (!isset($value['date'])) {
                            throw new Exception('Cannot cast an array to a date/time field, unless it follows DateTime array format');
                        }

                        $value = new \DateTime($value['date'] . (isset($value['timezone']) ? ' ' . $value['timezone'] : ''));
                    } elseif (is_string($value)) {
                        $value = new \DateTime($value);
                    } elseif (is_integer($value)) {
                        $value = \DateTime::createFromFormat('U', $value);
                    }
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

            // enter into the final cast data array
            $castData[$fieldName] = $value;
        }

        return $castData;
    }

    /**
     * Maps data into a resource or set of resources.
     * @param mixed $resource Resource to map data into.
     * @param mixed $data Data to be mapped.
     */
    abstract public function map($resource, $data);

    /**
     * Reverse maps data into the desired format.
     * @param mixed $data Data to reverse map.
     * @return mixed
     */
    abstract public function reverse($data);
}

