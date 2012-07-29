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

namespace BedRest\Mapping\Service;

use BedRest\Configuration;
use BedRest\Mapping\MappingException;

/**
 * ServiceMetadataFactory
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadataFactory
{
    /**
     * Configuration object.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Mapping metadata driver.
     * @var \BedRest\Mapping\Service\Driver\Driver
     */
    protected $driver;

    /**
     * Stores all loaded ServiceMetadata instances.
     * @var array
     */
    protected $loadedMetadata = array();

    /**
     * Constructor.
     * Initialises the factory with the set configuration.
     * @param \BedRest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->driver = $configuration->getServiceMetadataDriverImpl();
    }

    /**
     * Returns ServiceMetadata for the specified class.
     * @param string $className
     * @return \BedRest\Mapping\Service\ServiceMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->isService($className)) {
            throw MappingException::classIsNotMappedService($className);
        }

        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
        }

        return $this->loadedMetadata[$className];
    }

    /**
     * Returns the entire collection of ServiceMetadata objects for all mapped services.
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
     * @param string $class
     */
    protected function loadMetadata($class)
    {
        $metadata = new ServiceMetadata($class);

        // use the driver to load metadata
        $this->driver->loadMetadataForClass($class, $metadata);

        // store the metadata
        $this->loadedMetadata[$class] = $metadata;
    }

    /**
     * Whether the specified class is a mapped service.
     * @param string $className
     * @return boolean
     */
    public function isService($className)
    {
        return $this->driver->isService($className);
    }
}
