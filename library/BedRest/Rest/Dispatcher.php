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

use BedRest\Resource\Mapping\Exception as ResourceMappingException;
use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Rest\Event\Event;
use BedRest\Rest\Request\Request;
use BedRest\Rest\Response\Response;
use BedRest\Service\LocatorInterface;
use BedRest\Service\Mapping\ServiceMetadataFactory;

/**
 * Dispatcher
 *
 * Responsible for dispatching REST actions to the correct services. Sits between controllers and the service layer.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Dispatcher
{
    /**
     * @var \BedRest\Events\EventManager
     */
    protected $eventManager;

    /**
     * @var \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;

    /**
     * @var \BedRest\Service\LocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected $serviceMetadataFactory;

    /**
     * Returns the event manager.
     *
     * @return \BedRest\Events\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Sets the event manager instance.
     *
     * @param \BedRest\Events\EventManager $eventManager
     */
    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Returns the ResourceMetadataFactory used for retrieving resource metadata.
     *
     * @return \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    public function getResourceMetadataFactory()
    {
        return $this->resourceMetadataFactory;
    }

    /**
     * Sets the ResourceMetadataFactory used for retrieving resource metadata.
     *
     * @param \BedRest\Resource\Mapping\ResourceMetadataFactory $factory
     */
    public function setResourceMetadataFactory(ResourceMetadataFactory $factory)
    {
        $this->resourceMetadataFactory = $factory;
    }

    /**
     * Returns the service container.
     *
     * @return \BedRest\Service\LocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Sets the service container.
     *
     * @param \BedRest\Service\LocatorInterface $locator
     */
    public function setServiceLocator(LocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
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
     * Sets the ServiceMetadataFactory instance.
     *
     * @param \BedRest\Service\Mapping\ServiceMetadataFactory $factory
     */
    public function setServiceMetadataFactory(ServiceMetadataFactory $factory)
    {
        $this->serviceMetadataFactory = $factory;
    }

    /**
     * Processes a REST request, returning a Response object.
     *
     * @param  \BedRest\Rest\Request\Request   $request
     * @throws \BedRest\Rest\Exception
     * @return \BedRest\Rest\Response\Response
     */
    public function dispatch(Request $request)
    {
        // determine resource
        $resourceParts = explode('/', $request->getResource());
        $resourceName = $resourceParts[0];
        $subResourceName = false;
        if (count($resourceParts) > 1) {
            $subResourceName = $resourceParts[1];
        }

        try {
            $resourceMetadata = $this->resourceMetadataFactory->getMetadataByResourceName($resourceName);
        } catch (ResourceMappingException $e) {
            throw Exception::resourceNotFound($resourceName, 404, $e);
        }

        // determine service
        if ($subResourceName) {
            if (!$resourceMetadata->hasSubResource($subResourceName)) {
                throw Exception::resourceNotFound($request->getResource());
            }

            $subResourceMapping = $resourceMetadata->getSubResource($subResourceName);
            $service = $this->serviceLocator->get($subResourceMapping['service']);
        } else {
            $service = $this->serviceLocator->get($resourceMetadata->getService());
        }

        $this->registerListeners($service);

        $event = new Event();
        $event->setRequest($request);

        $this->eventManager->dispatch($request->getMethod(), $event);

        return $event->getData();
    }

    /**
     * Registers listeners for the supplied service instance. Listeners are obtained from ServiceMetadata for
     * the class of the instance.
     *
     * @param object $service
     */
    protected function registerListeners($service)
    {
        $serviceMetadata = $this->serviceMetadataFactory->getMetadataFor(get_class($service));

        foreach ($serviceMetadata->getAllListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventManager->addListener($event, array($service, $listener));
            }
        }
    }
}
