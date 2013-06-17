<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

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
