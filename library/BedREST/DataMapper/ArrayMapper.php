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
     * Maps data into a RestResource.
     * @param mixed $resource Entity to map the data into.
     * @param mixed $data Data to be mapped.
     */
    public function map($resource, $data)
    {
        // map the data over
        $data = $this->castData($resource, $data);
        
        foreach ($data as $property => $value) {
            $resource->$property = $value;
        }
    }
    
    /**
     * Maps data from a RestResource into the desired format.
     * @param mixed $resource Entity to map data from.
     * @return mixed
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
