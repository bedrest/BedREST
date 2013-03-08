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
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
     * @param  \BedRest\Service\Configuration  $configuration
     * @return \BedRest\Service\ServiceManager
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
     * Returns the DataMapper to be used by a service.
     * @param  string                          $service Class name of the service.
     * @throws \BedRest\Service\Exception
     * @throws \BedRest\Service\Data\Exception
     * @return \BedRest\Service\Data\Mapper
     */
    public function getDataMapper($service)
    {
        $serviceMetadata = $this->getServiceMetadata($service);

        // check the service exists
        if (!class_exists($service)) {
            throw Exception::serviceNotFound($service);
        }

        if (!$this->serviceMetadataFactory->isService($service)) {
            throw Exception::classNotMappedService($service);
        }

        // check the mapper exists
        $className = $serviceMetadata->getDataMapper();
        if (!class_exists($className)) {
            throw Data\Exception::dataMapperNotFound($className);
        }

        // use a DI container to instantiate the mapper
        $container = $this->configuration->getServiceContainer();

        $id = "{$className}";
        if (!$container->hasDefinition($id)) {
            $this->buildDataMapperDefinition($container, $serviceMetadata, $id, $className);
        }

        return $container->get($id);
    }

    /**
     * Builds a definition in a ContainerBuilder instance for the specified DataMapper class.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param Mapping\ServiceMetadata                                 $metadata
     * @param string                                                  $id
     * @param string                                                  $className
     */
    protected function buildDataMapperDefinition(
        ContainerBuilder $container,
        ServiceMetadata $metadata,
        $id,
        $className
    ) {
        $definition = $container->register($id, $className);

        $definition
            ->addArgument($this->getConfiguration())
            ->addArgument($this);

        // TODO: this should be drawn from the DataMapper itself perhaps?
        if ($metadata->getType() == ServiceMetadata::TYPE_DOCTRINE) {
            $definition
                ->addMethodCall('setEntityManager', array('%doctrine.entityManager%'));
        }
    }

    /**
     * Returns an instance of the service for the specified resource.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @throws \BedRest\Service\Exception
     * @return object
     */
    public function getService(ResourceMetadata $resourceMetadata)
    {
        $className = $resourceMetadata->getService();

        // check class exists and is denoted as a service
        if (!class_exists($className)) {
            throw new Exception("Service '$className' not found.");
        }

        if (!$this->serviceMetadataFactory->isService($className)) {
            throw new Exception("The class '{$className}' is not a mapped service.");
        }

        // use a DI container to instantiate the service
        $container = $this->configuration->getServiceContainer();

        $id = "{$className}#{$resourceMetadata->getName()}";
        if (!$container->hasDefinition($id)) {
            $serviceMetadata = $this->getServiceMetadata($className);
            $this->buildServiceDefinition($container, $serviceMetadata, $resourceMetadata, $id, $className);
        }

        return $container->get($id);
    }

    /**
     * Builds a definition in a ContainerBuilder instance for the specified service class.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \BedRest\Resource\Mapping\ResourceMetadata              $resourceMetadata
     * @param string                                                  $id
     * @param string                                                  $className
     * @param \BedRest\Service\Mapping\ServiceMetadata                $metadata
     */
    protected function buildServiceDefinition(
        ContainerBuilder $container,
        ServiceMetadata $metadata,
        ResourceMetadata $resourceMetadata,
        $id,
        $className
    ) {
        $definition = $container->register($id, $className);

        $definition
            ->addArgument($resourceMetadata, $this->getDataMapper($className));

        if ($metadata->getType() == ServiceMetadata::TYPE_DOCTRINE) {
            $definition
                ->addMethodCall('setEntityManager', array('%doctrine.entityManager%'));
        }
    }
}
