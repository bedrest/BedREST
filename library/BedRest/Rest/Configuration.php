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

namespace BedRest\Rest;

use BedRest\Resource\Mapping\Driver\Driver as ResourceDriver;
use Doctrine\Common\Cache\Cache;

/**
 * Configuration
 *
 * Configuration container for BedRest.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Configuration
{
    /**
     * Resource metadata driver.
     * @var \BedRest\Resource\Mapping\Driver\Driver
     */
    protected $resourceMetadataDriverImpl;

    /**
     * Resource cache implementation.
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $resourceMetadataCacheImpl;

    /**
     * Default service class name.
     * @var string
     */
    protected $defaultService = 'BedRest\Model\Doctrine\Service';

    /**
     * Allowable content types with associated converters.
     * @var array
     */
    protected $contentTypes = array(
        'application/json'
    );

    /**
     * Array of paths where resources should be auto-discovered.
     * @var array
     */
    protected $resourcePaths = array();

    /**
     * Sets the resource metadata driver implementation.
     * @param \BedRest\Resource\Mapping\Driver\Driver $driver
     */
    public function setResourceMetadataDriverImpl(ResourceDriver $driver)
    {
        $this->resourceMetadataDriverImpl = $driver;
    }

    /**
     * Returns the resource metadata driver implementation.
     * @return \BedRest\Resource\Mapping\Driver\Driver
     */
    public function getResourceMetadataDriverImpl()
    {
        return $this->resourceMetadataDriverImpl;
    }

    /**
     * Sets the resource metadata cache implementation.
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function setResourceMetadataCacheImpl(Cache $cache)
    {
        $this->resourceMetadataCacheImpl = $cache;
    }

    /**
     * Returns the resource metadata cache implementation.
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getResourceMetadataCacheImpl()
    {
        return $this->resourceMetadataCacheImpl;
    }

    /**
     * Sets the default service class name.
     * @param string $className
     */
    public function setDefaultService($className)
    {
        $this->defaultService = $className;
    }

    /**
     * Returns the default service class name.
     * @return string
     */
    public function getDefaultService()
    {
        return $this->defaultService;
    }

    /**
     * Sets the allowed content types for responses.
     * @param array $contentTypes
     */
    public function setContentTypes(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * Returns the allowable content types for responses.
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * Sets a group of paths in which resources can be auto-discovered.
     * @param array $paths
     */
    public function setResourcePaths(array $paths)
    {
        $this->resourcePaths = $paths;
    }

    /**
     * Returns the paths in which resources can be auto-discovered.
     * @return array
     */
    public function getResourcePaths()
    {
        return $this->resourcePaths;
    }
}
