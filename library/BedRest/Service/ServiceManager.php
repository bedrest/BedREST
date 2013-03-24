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
     * Stores all loaded service instances.
     * 
     * @var array
     */
    protected $loadedServices;

    /**
     * Service metadata factory.
     * 
     * @var \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;
    
    /**
     * Service container.
     * 
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $serviceContainer;

    /**
     * Constructor.
     * 
     * @return \BedRest\Service\ServiceManager
     */
    public function __construct()
    {
    }

    /**
     * Sets the ServiceMetadataFactory instance.
     *
     * @param \BedRest\Service\Mapping\ServiceMetadataFactory $factory
     */
    public function setServiceMetadataFactory(ServiceMetadataFactory $factory)
    {
        $this->serviceMetadataFactory = $factory;
    }

    /**
     * Returns the service metadata factory.
     *
     * @return \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    public function getServiceMetadataFactory()
    {
        return $this->serviceMetadataFactory;
    }

    /**
     * Sets the service container.
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function setServiceContainer(ContainerBuilder $container)
    {
        $this->serviceContainer = $container;
    }

    /**
     * Returns the service container.
     * 
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    /**
     * Returns service metadata for a class.
     * 
     * @param  string                                   $className
     * @return \BedRest\Service\Mapping\ServiceMetadata
     */
    public function getServiceMetadata($className)
    {
        return $this->serviceMetadataFactory->getMetadataFor($className);
    }

    /**
     * Returns the DataMapper to be used by a service.
     * 
     * @param  string                          $service Class name of the service.
     * @throws \BedRest\Service\Exception
     * @throws \BedRest\Service\Data\Exception
     * @return \BedRest\Service\Data\Mapper
     */
    public function getDataMapper($service)
    {
        // check the service exists
        if (!class_exists($service)) {
            throw Exception::serviceNotFound($service);
        }

        if (!$this->serviceMetadataFactory->isService($service)) {
            throw Exception::classNotMappedService($service);
        }

        // check the mapper exists
        $serviceMetadata = $this->getServiceMetadata($service);
        $className = $serviceMetadata->getDataMapper();
        if (!class_exists($className)) {
            throw Data\Exception::dataMapperNotFound($className);
        }

        // use a DI container to instantiate the mapper
        $id = "{$className}";
        if (!$this->serviceContainer->hasDefinition($id)) {
            $this->buildDataMapperDefinition($serviceMetadata, $id, $className);
        }

        return $this->serviceContainer->get($id);
    }

    /**
     * Builds a definition in a ContainerBuilder instance for the specified DataMapper class.
     * 
     * @param Mapping\ServiceMetadata                                 $metadata
     * @param string                                                  $id
     * @param string                                                  $className
     */
    protected function buildDataMapperDefinition(
        ServiceMetadata $metadata,
        $id,
        $className
    ) {
        $definition = $this->serviceContainer->register($id, $className);
        $definition->addArgument($this);

        // TODO: this should be drawn from the DataMapper itself perhaps?
        if ($metadata->getType() == ServiceMetadata::TYPE_DOCTRINE) {
            $definition
                ->addMethodCall('setEntityManager', array('%doctrine.entityManager%'));
        }
    }

    /**
     * Returns an instance of the service for the specified resource.
     * 
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @throws \BedRest\Service\Exception
     * @return object
     */
    public function getService(ResourceMetadata $resourceMetadata)
    {
        $className = $resourceMetadata->getService();

        // check class exists and is denoted as a service
        if (!class_exists($className)) {
            throw Exception::serviceNotFound($className);
        }

        if (!$this->serviceMetadataFactory->isService($className)) {
            throw Exception::classNotMappedService($className);
        }

        // use a DI container to instantiate the service
        $id = "{$className}#{$resourceMetadata->getName()}";
        if (!$this->serviceContainer->hasDefinition($id)) {
            $serviceMetadata = $this->getServiceMetadata($className);
            $this->buildServiceDefinition(
                $serviceMetadata,
                $resourceMetadata,
                $id,
                $className
            );
        }

        return $this->serviceContainer->get($id);
    }

    /**
     * Builds a definition in a ContainerBuilder instance for the specified service class.
     * 
     * @param \BedRest\Resource\Mapping\ResourceMetadata              $resourceMetadata
     * @param string                                                  $id
     * @param string                                                  $className
     * @param \BedRest\Service\Mapping\ServiceMetadata                $metadata
     */
    protected function buildServiceDefinition(
        ServiceMetadata $metadata,
        ResourceMetadata $resourceMetadata,
        $id,
        $className
    ) {
        $definition = $this->serviceContainer->register($id, $className);
        $definition
            ->addArgument($resourceMetadata)
            ->addArgument($this->getDataMapper($className));

        if ($metadata->getType() == ServiceMetadata::TYPE_DOCTRINE) {
            $definition
                ->addMethodCall('setEntityManager', array('%doctrine.entityManager%'));
        }
    }
}
