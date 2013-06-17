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

namespace BedRest\Resource\Mapping;

/**
 * Exception
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Exception extends \Exception
{
    /**
     * Thrown when a class is not a mapped resource.
     *
     * @param string $className
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function classIsNotMappedResource($className)
    {
        return new self("Class '{$className}' is not a mapped resource.");
    }

    /**
     * Thrown when a resource cannot be found.
     *
     * @param string $resourceName
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function resourceNotFound($resourceName)
    {
        return new self("Resource '{$resourceName}' not found.");
    }

    /**
     * Thrown when no paths have been supplied.
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function pathsRequired()
    {
        return new self("A set of paths must be provided in order to discover classes.");
    }

    /**
     * Thrown when an invalid path has been supplied.
     *
     * @param $path
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function invalidPath($path)
    {
        return new self("The path '{$path}' is invalid.");
    }

    /**
     * Thrown when a set of invalid sub-resources have been supplied.
     *
     * @param string $className
     *
     * @return \BedRest\Resource\Mapping\Exception
     */
    public static function invalidSubResources($className)
    {
        return new self("Invalid set of sub-resources supplied for class '$className''.");
    }
}
