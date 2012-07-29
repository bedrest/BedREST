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
     * @var \BedRest\ServiceManagaer
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
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

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
     * Returns resource metadata for a class.
     * @param string $className
     * @return \BedRest\Mapping\Resource\ResourceMetadata
     */
    public function getResourceMetadata($className)
    {
        return $this->resourceMetadataFactory->getMetadataFor($className);
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
     * Sets the service manager.
     * @param \BedRest\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns the service manager.
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
     * Processes a REST request, returning a Response object.
     * @param \BedRest\Request $request
     */
    public function process(Request $request)
    {
        $rm = $this->getResourceMetadata($request->getResource());

        $sm = $this->getServiceManager();
        $service = $sm->getService($rm->getServiceClass());

        // determine event
        $method = $rm->getServiceMethod($request->getMethod());

        // TODO: create event

        // TODO: dispatch event
        //$this->getEventManager()->dispatchEvent($eventName, $eventArgs);

        return new Response();
    }
}

