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

namespace BedRest\Service;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Service\Configuration;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Service\Mapping\ServiceMetadataFactory;

/**
 * ServiceManager
 *
 * Service manager for controlling instances of various services in use by
 * the system.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceManager
{
    /**
     * BedRest configuration.
     * @var \BedRest\Service\Configuration
     */
    protected $configuration;

    /**
     * Stores all loaded service instances.
     * @var array
     */
    protected $loadedServices;

    /**
     * Service metadata factory.
     * @var \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;

    /**
     * Constructor.
     * @param \BedRest\Service\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->serviceMetadataFactory = new ServiceMetadataFactory($configuration);
    }

    /**
     * Returns the configuration object.
     * @return \BedRest\Service\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns service metadata for a class.
     * @param  string                                   $className
     * @return \BedRest\Service\Mapping\ServiceMetadata
     */
    public function getServiceMetadata($className)
    {
        return $this->serviceMetadataFactory->getMetadataFor($className);
    }

    /**
     * Returns the service metadata factory.
     * @return \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    public function getServiceMetadataFactory()
    {
        return $this->serviceMetadataFactory;
    }

    /**
     * Returns an instance of the service for the specified resource.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @return object
     */
    public function getService(ResourceMetadata $resourceMetadata)
    {
        $hash = $this->getServiceHash($resourceMetadata);

        if (!$this->hasService($resourceMetadata)) {
            $this->loadedServices[$resourceMetadata->getService()][$hash]
                = $this->loadService($resourceMetadata);
        }

        return $this->loadedServices[$resourceMetadata->getService()][$hash];
    }

    /**
     * Whether a service has been loaded or not yet.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @return boolean
     */
    protected function hasService(ResourceMetadata $resourceMetadata)
    {
        $hash = $this->getServiceHash($resourceMetadata);

        if (isset($this->loadedServices[$resourceMetadata->getService()][$hash])) {
            return true;
        }

        return false;
    }

    /**
     * Loads the specified service class.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @throws \BedRest\Service\Exception
     * @return object
     */
    protected function loadService(ResourceMetadata $resourceMetadata)
    {
        $className = $resourceMetadata->getService();

        // check class exists and is denoted as a service
        if (!class_exists($className)) {
            throw new Exception("Service '$className' not found.");
        }

        if (!$this->serviceMetadataFactory->isService($className)) {
            throw new Exception("The class '{$className}' is not a mapped service.");
        }

        // instantiate the class
        $service = new $className($resourceMetadata);

        return $service;
    }

    /**
     * Gets the hash used for indexing loaded services.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @return string
     */
    protected function getServiceHash(ResourceMetadata $resourceMetadata)
    {
        $hash = spl_object_hash($resourceMetadata);

        return $hash;
    }
}
