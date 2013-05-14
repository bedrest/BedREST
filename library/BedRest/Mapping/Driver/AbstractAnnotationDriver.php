<?php

namespace BedRest\Mapping\Driver;

/**
 * AbstractAnnotationDriver
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
abstract class AbstractAnnotationDriver
{
    /**
     * Returns an annotation instance if it is in the collection, otherwise false.
     *
     * @param array  $annotations
     * @param string $className
     *
     * @return object|bool
     */
    protected function getAnnotation(array $annotations, $className)
    {
        if (isset($annotations[$className])) {
            return $annotations[$className];
        }

        return false;
    }

    /**
     * Indexes a numerically-indexed array of annotation instances by their class names.
     *
     * @param array $annotations
     *
     * @return array
     */
    protected function indexAnnotationsByType(array $annotations)
    {
        $indexed = array();

        // if we are receiving annotations indexed by number, transform it to by class name
        if ($annotations && is_numeric(key($annotations))) {
            foreach ($annotations as $annotation) {
                $annotationType = get_class($annotation);

                if (isset($indexed[$annotationType]) && !is_array($indexed[$annotationType])) {
                    $indexed[$annotationType] = array($indexed[$annotationType], $annotation);
                } elseif (isset($indexed[$annotationType])) {
                    $indexed[$annotationType][] = $annotation;
                } else {
                    $indexed[$annotationType] = $annotation;
                }
            }
        }

        return $indexed;
    }
}
