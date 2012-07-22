<?php

namespace BedRest\Mapping;

use BedRest\Mapping\Driver\Driver;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

/**
 * ResourceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactory
{
    /**
     * Mapping metadata driver.
     * @var BedRest\Mapping\Driver\Driver
     */
    protected $driver;
    
    /**
     * ClassMetadataFactory instance
     * @var Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    protected $classMetadataFactory;
    
    /**
     * Stores all loaded ResourceMetadata instances.
     * @var array
     */
    protected $loadedMetadata = array();
    
    /**
     * Sets the metadata driver.
     * @param BedRest\Mapping\Driver\Driver $driver 
     */
    public function setMetadataDriver(Driver $driver)
    {
        $this->driver = $driver;
    }
    
    /**
     * Returns the metadata driver.
     * @return BedRest\Mapping\Driver\Driver
     */
    public function getMetadataDriver()
    {
        return $this->driver;
    }
    
    /**
     * Sets the ClassMetadataFactory instance.
     * @param Doctrine\ORM\Mapping\ClassMetadataFactory $factory 
     */
    public function setClassMetadataFactory(ClassMetadataFactory $factory)
    {
        $this->classMetadataFactory = $factory;
    }
    
    /**
     * Returns the ClassMetadataFactory instance.
     * @return Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    public function getClassMetadataFactory()
    {
        return $this->classMetadataFactory;
    }
    
    /**
     * Returns ResourceMetadata for the specified class.
     * @param string $className
     * @return BedRest\Mapping\ResourceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
        }
        
        return $this->loadedMetadata[$className];
    }
    
    protected function loadMetadata($className)
    {
        $resource = new ResourceMetadata($className);
        
        // load ClassMetadata
        $classMetadata = $this->classMetadataFactory->getMetadataFor($className);
        $resource->setClassMetadata($classMetadata);
        
        // use the driver to load metadata
        $this->driver->loadMetadataForClass($className, $resource);
        
        // store the metadata
        $this->loadedMetadata[$className] = $resource;
    }
}
