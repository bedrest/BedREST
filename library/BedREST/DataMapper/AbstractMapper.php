<?php

namespace BedREST\DataMapper;

use Doctrine\ORM\EntityManager,
    Doctrine\DBAL\Types\Type;

/**
 * BedREST\Model\AbstractMapper
 * 
 * @author Geoff Adams <geoff@dianode.net>
 */
abstract class AbstractMapper
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     * Initialises the data mapper with the supplied options.
     * @param Adapter\AdapterInterface $adapter 
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $key => $value) {
            $setterMethod = 'set' . ucfirst($key);
            
            if (method_exists($this, $setterMethod)) {
                $this->$setterMethod($value);
            }
        }
    }
    
    /**
     * Gets the entity manager.
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager instanceof EntityManager) {
            throw new DataMappingException('EntityManager not provided');
        }
        
        return $this->entityManager;
    }

    /**
     * Sets the entity manager.
     * @param Doctrine\ORM\EntityManager $em 
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Takes an input array of data and a resource, then proceeds to process each
     * property of the resource by finding data and casting it to the appropriate
     * format.
     * @param RestResource $resource
     * @param array $data
     * @return array
     * @throws DataMappingException 
     */
    public function castData($resource, array $data)
    {
        // get the class meta data for the entity
        $em = $this->getEntityManager();
        
        $classMetaInfo = $em->getClassMetadata(get_class($resource));

        // process basic fields
        $castData = array();
        
        foreach ($classMetaInfo->fieldMappings as $fieldName => $fieldMapping) {
            // skip fields not included in the data
            if (!isset($data[$fieldName])) continue;

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
                    if (!$value instanceof \DateTime) {
                        $value = new \DateTime($value);
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
                    throw new DataMappingException('"object" type mapping is not currently supported');
                    break;
                case TYPE::BLOB:
                    throw new DataMappingException('"blob" type mapping is not currently supported');
                    break;
                default:
                    throw new DataMappingException("Unknown type \"{$fieldMapping['type']}\"");
                    break;
            }
            
            // enter into the final cast data array
            $castData[$fieldName] = $value;
    	}
        
        return $castData;
    }
    
    /**
     * Maps data into a RestResource.
     * @param RestResource $resource Entity to map the data into.
     * @param mixed $data Data to be mapped.
     */
    abstract public function map($resource, $data);
    
    /**
     * Maps data from a RestResource into the desired format.
     * @param RestResource $resource Entity to map data from.
     * @return mixed
     */
    abstract public function reverse($resource);
}
