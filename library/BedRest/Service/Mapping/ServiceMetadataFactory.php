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

namespace BedRest\Service\Mapping;

use BedRest\Service\Mapping\Exception;
use BedRest\Service\Mapping\Driver\Driver;
use Doctrine\Common\Cache\Cache;

/**
 * ServiceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadataFactory
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
    protected $cacheSuffix = '\$SERVICEMETADATA';

    /**
     * Cache driver to use.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Mapping metadata driver.
     *
     * @var \BedRest\Service\Mapping\Driver\Driver
     */
    protected $driver;

    /**
     * Stores all loaded ServiceMetadata instances.
     *
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * Constructor.
     *
     * @param \BedRest\Service\Mapping\Driver\Driver $driver
     * @param \Doctrine\Common\Cache\Cache           $cache
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
     * Returns ServiceMetadata for the specified class.
     *
     * @param  string                                   $className
     * @throws \BedRest\Service\Mapping\Exception
     * @return \BedRest\Service\Mapping\ServiceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->isService($className)) {
            throw Exception::classIsNotMappedService($className);
        }

        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
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
     * Returns the entire collection of ServiceMetadata objects for all mapped services.
     *
     * @return array
     */
    public function getAllMetadata()
    {
        foreach ($this->driver->getAllClassNames() as $className) {
            if (!isset($this->loadedMetadata[$className])) {
                $this->loadMetadata($className);
            }
        }

        return $this->loadedMetadata;
    }

    /**
     * Loads the ServiceMetadata for the supplied class.
     *
     * @param string $class
     */
    protected function loadMetadata($class)
    {
        // first run, we have no parent
        $parent = null;

        $parentClasses = $this->getParentClasses($class);
        $parentClasses[] = $class;

        // iterate through the list of mapped service parent classes
        foreach ($parentClasses as $parentClass) {
            // create an empty metadata class
            $class = new ServiceMetadata($class);

            // copy all data from the immediate parent, if present
            if ($parent) {
                $class->setClassName($parent->getClassName());
                $class->setAllListeners($parent->getAllListeners());
            }

            // now overlay the metadata from the class itself
            if (!isset($this->loadedMetadata[$parentClass])) {
                $this->driver->loadMetadataForClass($parentClass, $class);
                $this->loadedMetadata[$parentClass] = $class;
            }

            // the parent for the next iteration will be this iteration
            $parent = $class;
        }
    }

    /**
     * Whether the specified class is a mapped service.
     *
     * @param  string  $className
     * @return boolean
     */
    public function isService($className)
    {
        return $this->driver->isService($className);
    }

    /**
     * Returns the list of parent service classes for the specified class.
     *
     * @param  string $className
     * @return array
     */
    protected function getParentClasses($className)
    {
        $parents = array();

        foreach (array_reverse(class_parents($className)) as $class) {
            if ($this->driver->isService($class)) {
                $parents[] = $class;
            }
        }

        return $parents;
    }
}
