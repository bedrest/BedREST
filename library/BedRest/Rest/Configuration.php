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
     * Default resource handler class name.
     * @var string
     */
    protected $defaultResourceHandler = 'BedRest\Resource\Handler\SimpleDoctrineHandler';

    /**
     * Default service class name.
     * @var string
     */
    protected $defaultService = 'BedRest\Service\SimpleDoctrineService';

    /**
     * Allowable content types with associated converters.
     * @var array
     */
    protected $contentTypes = array(
        'application/json'
    );

    /**
     * Mapping of content type to converter class name.
     * @var array
     */
    protected $contentConverters = array(
        'application/json' => 'BedRest\Content\Converter\JsonConverter'
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
     * Sets the default resource handler class name.
     * @param string $resourceHandler
     */
    public function setDefaultResourceHandler($resourceHandler)
    {
        $this->defaultResourceHandler = $resourceHandler;
    }

    /**
     * Returns the default resource handler class name.
     * @return string
     */
    public function getDefaultResourceHandler()
    {
        return $this->defaultResourceHandler;
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
     * Sets the mapping between content types and content converters.
     * @param array $contentConverters
     */
    public function setContentConverters(array $contentConverters)
    {
        $this->contentConverters = $contentConverters;
    }

    /**
     * Returns the content type mappings.
     * @return array
     */
    public function getContentConverters()
    {
        return $this->contentConverters;
    }

    /**
     * Returns the content converter for the supplied content type, if available.
     * @param  string         $contentType
     * @return string|boolean
     */
    public function getContentConverter($contentType)
    {
        if (!isset($this->contentConverters[$contentType])) {
            return null;
        }

        return $this->contentConverters[$contentType];
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
