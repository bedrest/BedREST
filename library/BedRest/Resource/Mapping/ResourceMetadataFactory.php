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

use BedRest\Resource\Mapping\Driver\Driver;
use BedRest\Resource\Mapping\Exception;
use Doctrine\Common\Cache\Cache;

/**
 * ResourceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactory
{
    /**
     * Prefix for cache IDs.
     *
     * @var string
     */
    protected $cachePrefix = 'BEDREST::';

    /**
     * Suffix for cache IDs.
     *
     * @var string
     */
    protected $cacheSuffix = '\$RESOURCEMETADATA';

    /**
     * Cache driver to use.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Mapping metadata driver.
     *
     * @var \BedRest\Resource\Mapping\Driver\Driver
     */
    protected $driver;

    /**
     * Stores all loaded ResourceMetadata instances.
     *
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * Stores a map of resource names to class names.
     *
     * @var array
     */
    protected $resourceClassMap = array();

    /**
     * Constructor.
     *
     * @param \BedRest\Resource\Mapping\Driver\Driver $driver
     * @param \Doctrine\Common\Cache\Cache            $cache
     */
    public function __construct(Driver $driver, Cache $cache = null)
    {
        $this->driver = $driver;
        $this->cache = $cache;
    }

    /**
     * Sets the cache driver for this factory instance.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function setCache(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the cache driver in use by this factory instance.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns ResourceMetadata for the specified class.
     *
     * @param  string                                     $className
     * @throws \BedRest\Resource\Mapping\Exception
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->isResource($className)) {
            throw Exception::classIsNotMappedResource($className);
        }

        if (isset($this->loadedMetadata[$className])) {
            return $this->loadedMetadata[$className];
        }

        if ($this->cache) {
            $cacheId = $this->cachePrefix . $className . $this->cacheSuffix;

            if (($cached = $this->cache->fetch($cacheId)) !== false) {
                $this->loadedMetadata[$className] = $cached;
            } else {
                $this->loadMetadata($className);
                $this->cache->save($cacheId, $this->loadedMetadata[$className], null);
            }
        } else {
            $this->loadMetadata($className);
        }

        return $this->loadedMetadata[$className];
    }

    /**
     * Returns ResourceMetadata for the specified resource.
     *
     * @param  string                                     $resourceName
     * @throws \BedRest\Resource\Mapping\Exception
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getMetadataByResourceName($resourceName)
    {
        $this->getAllMetadata();

        if (!isset($this->resourceClassMap[$resourceName])) {
            throw Exception::resourceNotFound($resourceName);
        }

        return $this->loadedMetadata[$this->resourceClassMap[$resourceName]];
    }

    /**
     * Returns the entire collection of ResourceMetadata objects for all mapped resources. Entities not marked as
     * resources are not included.
     *
     * @return array
     */
    public function getAllMetadata()
    {
        $resourceClasses = $this->driver->getAllClassNames();

        foreach ($resourceClasses as $class) {
            $this->getMetadataFor($class);
        }

        return $this->loadedMetadata;
    }

    /**
     * Loads the ResourceMetadata for the specified class.
     *
     * @param string $className
     */
    protected function loadMetadata($className)
    {
        $resource = new ResourceMetadata($className);

        $this->driver->loadMetadataForClass($className, $resource);

        $this->loadedMetadata[$className] = $resource;
        $this->resourceClassMap[$resource->getName()] = $className;
    }

    /**
     * Whether the specified class is a mapped resource.
     *
     * @param  string  $className
     * @return boolean
     */
    public function isResource($className)
    {
        return $this->driver->isResource($className);
    }
}
