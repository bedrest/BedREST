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
use BedRest\EventManager;
use BedRest\ServiceManager;
use BedRest\Mapping\Resource\ResourceMetadata;
use BedRest\Mapping\Resource\ResourceMetadataFactory;

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
     * Constructor.
     * @param \BedRest\Configuration $configuration
     * @param \BedRest\EventManager $eventManager
     * @param \BedRest\ServiceManager $serviceManager
     */
    public function __construct(Configuration $configuration, EventManager $eventManager, ServiceManager $serviceManager)
    {
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        $this->serviceManager = $serviceManager;

        $this->resourceMetadataFactory = new ResourceMetadataFactory($configuration);
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
     * Returns the service manager instance.
     * @return \BedRest\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
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
     * Processes a REST request, returning a Response object.
     * @param \BedRest\Request $request
     */
    public function process(Request $request)
    {
        // create an empty response
        $response = new Response($this->configuration);

        // establish the best content type
        $contentType = $request->getAcceptBestMatch($this->configuration->getContentTypes());

        if (!$contentType) {
            throw RestException::notAcceptable();
        }

        $response->setContentType($contentType);

        // get metadata
        $resourceMetadata = $this->getResourceMetadataByName($request->getResource());

        // get the service
        $service = $this->serviceManager->getService($resourceMetadata->getServiceClass(), $this, $resourceMetadata->getClassName());

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
}

