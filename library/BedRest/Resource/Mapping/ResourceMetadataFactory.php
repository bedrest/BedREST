<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace BedRest\Resource\Mapping;

use BedRest\Rest\Configuration;
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
     * @var string
     */
    protected $cachePrefix = 'BEDREST::';

    /**
     * Suffix for cache IDs.
     * @var string
     */
    protected $cacheSuffix = '\$RESOURCEMETADATA';

    /**
     * Configuration object.
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

    /**
     * Cache driver to use.
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Mapping metadata driver.
     * @var \BedRest\Resource\Mapping\Driver\Driver
     */
    protected $driver;

    /**
     * Stores all loaded ResourceMetadata instances.
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * Stores a map of resource names to class names.
     * @var array
     */
    protected $resourceClassMap = array();

    /**
     * Constructor.
     * Initialises the factory with the set configuration.
     * @param \BedRest\Rest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->driver = $configuration->getResourceMetadataDriverImpl();
        $this->cache = $configuration->getResourceMetadataCacheImpl();
    }

    /**
     * Sets the cache driver for this factory instance.
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function setCache(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the cache driver in use by this factory instance.
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns ResourceMetadata for the specified class.
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
     * @param string $className
     */
    protected function loadMetadata($className)
    {
        $resource = new ResourceMetadata($className);
        $resource->setService($this->configuration->getDefaultService());

        // use the driver to load metadata
        $this->driver->loadMetadataForClass($className, $resource);

        // store the metadata
        $this->loadedMetadata[$className] = $resource;
        $this->resourceClassMap[$resource->getName()] = $className;
    }

    /**
     * Whether the specified class is a mapped resource.
     * @param  string  $className
     * @return boolean
     */
    public function isResource($className)
    {
        return $this->driver->isResource($className);
    }
}
