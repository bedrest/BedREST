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
 * ResourceMetadata
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadata
{
    /**
     * Name of the resource.
     * @var string
     */
    protected $name;

    /**
     * Class name of this resource.
     * @var string
     */
    protected $className;

    /**
     * Name of the service for this resource.
     * @var string
     */
    protected $service;

    /**
     * Identifier fields for the resource.
     * @var array
     */
    protected $identifierFields = array();

    /**
     * A set of allowable sub-resources belonging to this resource.
     * @var array
     */
    protected $subResources = array();

    /**
     * Constructor.
     * @param $className
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Sets the name of the resource.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of the resource.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the service for this resource.
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Returns the service for this resource.
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Sets the resource class name.
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the resource class name.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Sets the set of allowable sub-resources.
     * @param  array                               $subResources
     * @throws \BedRest\Resource\Mapping\Exception
     */
    public function setSubResources(array $subResources)
    {
        foreach ($subResources as $name => $mapping) {
            if (!is_string($name) || !is_array($mapping)) {
                throw Exception::invalidSubResources($this->className);
            }

            if (!isset($mapping['fieldName'])) {
                throw Exception::invalidSubResources($this->className);
            }

            if (!isset($mapping['service'])) {
                $mapping['service'] = null;
            }

            $this->subResources[$name] = $mapping;
        }
    }

    /**
     * Returns the set of allowable sub-resources.
     * @return array
     */
    public function getSubResources()
    {
        return $this->subResources;
    }

    /**
     * Whether the specified sub-resource exists or not.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSubResource($name)
    {
        return isset($this->subResources[$name]);
    }

    /**
     * Retrieves a sub-resource by name.
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getSubResource($name)
    {
        if ($this->hasSubResource($name)) {
            return $this->subResources[$name];
        }

        return null;
    }
}
