<?php

namespace BedREST\DataMapper;

/**
 * BedREST\DataMapper\Adapter\PhpArray
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ArrayMapper extends AbstractMapper
{
    /**
     * Maps data into an entity from an array.
     * @param mixed $resource Entity to map the data into.
     * @param array $data Data to be mapped.
     */
    public function map($resource, $data)
    {
        if (!is_array($data)) {
            throw new DataMappingException('Supplied data is not an array');
        }
        
        $data = $this->castData($resource, $data);
        
        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }
    
    /**
     * Maps data from an entity into an array.
     * @param mixed $resource Entity to map data from.
     * @return array
     */
    public function reverse($resource)
    {
        $classMetadata = $this->getEntityManager()->getClassMetadata(get_class($resource));
        
        $return = array();
        foreach ($classMetadata->fieldMappings as $property => $mapping) {
            $return[$property] = $resource->$property;
        }
        
        return $return;
    }
}
