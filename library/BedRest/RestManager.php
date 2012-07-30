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

namespace BedRest;

use BedRest\Configuration;
use BedRest\Request;
use BedRest\Response;
use BedRest\Event;
use BedRest\Mapping\Resource\ResourceMetadata;
use BedRest\Mapping\Resource\ResourceMetadataFactory;
use BedRest\Mapping\Service\ServiceMetadata;
use BedRest\Mapping\Service\ServiceMetadataFactory;

/**
 * RestManager
 *
 * Responsible for dispatching REST actions to the correct services. Sits between controllers and the service layer.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RestManager
{
    /**
     * Configuration instance.
     * @var \BedRest\Configuration
     */
    protected $configuration;

    /**
     * Service manager instance.
     * @var \BedRest\ServiceManager
     */
    protected $serviceManager;

    /**
     * Event manager instance.
     * @var \BedRest\EventManager
     */
    protected $eventManager;

    /**
     * The resource metadata factory.
     * @var \BedRest\Mapping\Resource\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;
    
    /**
     * The service metadata factory.
     * @var \BedRest\Mapping\Service\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;

    /**
     * Constructor.
     * @param \BedRest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->resourceMetadataFactory = new ResourceMetadataFactory($configuration);
        $this->serviceMetadataFactory = new ServiceMetadataFactory($configuration);
    }

    /**
     * Returns the configuration object.
     * @return \BedRest\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * Sets the service manager instance.
     * @param \BedRest\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * Returns the service manager instance.
     * @return \BedRest\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
    
    /**
     * Sets the event manager.
     * @param \BedRest\EventManager $eventManager
     */
    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
    
    /**
     * Returns the event manager.
     * @return \BedRest\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Returns resource metadata for a class.
     * @param string $className
     * @return \BedRest\Mapping\Resource\ResourceMetadata
     */
    public function getResourceMetadata($className)
    {
        return $this->resourceMetadataFactory->getMetadataFor($className);
    }
    
    /**
     * Returns resource metadata by resource name.
     * @param string $name
     * @return \BedRest\Mapping\Resource\ResourceMetadata
     */
    public function getResourceMetadataByName($name)
    {
        return $this->resourceMetadataFactory->getMetadataByResourceName($name);
    }

    /**
     * Returns the resource metadata factory.
     * @return \BedRest\Mapping\Resource\ResourceMetadataFactory
     */
    public function getResourceMetadataFactory()
    {
        return $this->resourceMetadataFactory;
    }
    
    /**
     * Returns service metadata for a class.
     * @param string $className
     * @return \BedRest\Mapping\Service\ServiceMetadata
     */
    public function getServiceMetadata($className)
    {
        return $this->serviceMetadataFactory->getMetadataFor($className);
    }
    
    /**
     * Returns the service metadata factory.
     * @return \BedRest\Mapping\Service\ServiceMetadataFactory
     */
    public function getServiceMetadataFactory()
    {
        return $this->serviceMetadataFactory;
    }

    /**
     * Processes a REST request, returning a Response object.
     * @param \BedRest\Request $request
     */
    public function process(Request $request)
    {
        // create an empty response
        $response = new Response($this->configuration);
        
        // TODO: load the allowable formats from config
        $bestMatch = $request->getAcceptBestMatch(array('application/json'));
        
        if (!$bestMatch) {
            throw RestException::notAcceptable();
        }
        
        $response->setContentType($bestMatch);
        
        // get metadata
        $resourceMetadata = $this->getResourceMetadataByName($request->getResource());
        $serviceMetadata = $this->getServiceMetadata($resourceMetadata->getServiceClass());
        
        // get the service
        $service = $this->getService($serviceMetadata, $resourceMetadata);

        // create event
        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $this->dispatchGetEntity($request, $response);
                break;
            case Request::METHOD_GET_COLLECTION:
                $this->dispatchGetCollection($request, $response);
                break;
            // TODO: implement other methods
            default:
                // TODO: exception, unknown request type
                break;
        }

        return $response;
    }
    
    protected function dispatchGetEntity(Request $request, Response $response)
    {
        $event = new Event\GetEntityEvent();
        
        $event->setRestManager($this);
        $event->setRequest($request);
        $event->setResponse($response);
        
        $event->setIdentifier($request->getRouteComponent('identifier'));
        
        $this->getEventManager()->dispatch('getEntity', $event);
    }
    
    protected function dispatchGetCollection($request, $response)
    {
        $event = new Event\GetCollectionEvent();
        
        $event->setRestManager($this);
        $event->setRequest($request);
        $event->setResponse($response);
        
        $this->getEventManager()->dispatch('getCollection', $event);
    }
    
    /**
     * Gets a service and binds any event listeners if this is the first time it has been requested.
     * @param \BedRest\Mapping\Service\ServiceMetadata $serviceMetadata
     */
    protected function getService(ServiceMetadata $serviceMetadata, ResourceMetadata $resourceMetadata)
    {
        $eventsLoaded = $this->serviceManager->hasService($serviceMetadata->getClassName(), $this, $resourceMetadata->getClassName());
        
        $service = $this->serviceManager->getService($serviceMetadata->getClassName(), $this, $resourceMetadata->getClassName());
        
        if (!$eventsLoaded) {
            foreach ($serviceMetadata->getAllListeners() as $event => $observers) {
                foreach ($observers as $observer) {
                    $this->eventManager->addListener($event, array($service, $observer));
                }
            }
        }
        
        return $service;
    }
}

