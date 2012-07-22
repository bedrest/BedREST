<?php

namespace BedRest\Mapping\Driver;

use BedRest\Mapping\ResourceMetadata;
use BedRest\Mapping\Driver\Driver;

/**
 * AnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriver implements Driver
{
    protected $annotationReader;
    
    public function loadMetadataForClass($className, ResourceMetadata $resourceMetadata)
    {
        $reflClass = $resourceMetadata->getClassMetadata()->getReflectionClass();
        
        $classAnnotations = $this->annotationReader->getClassAnnotation($reflClass);
        
        if ($classAnnotations && is_numeric(key($classAnnotations))) {
            foreach ($classAnnotations as $annotation) {
                $classAnnotations[get_class($annotation)] = $annotation;
            }
        }
        
        if (isset($classAnnotations['BedRest\Mapping\Resource'])) {
            $resourceAnnotation = $classAnnotations['BedRest\Mapping\Resource'];
            
            if (!empty($resourceAnnotation->name)) {
                $resourceMetadata->setName($resourceAnnotation->name);
            } else {
                $resourceMetadata->setName(substr($className, strrpos($className, '\\') + 1));
            }
            
            if (!empty($resourceAnnotation->serviceClass)) {
                $resourceMetadata->setName($resourceAnnotation->serviceClass);
            } else {
                throw MappingException::serviceClassNotProvided($className);
            }
        }
        
        
    }
}
