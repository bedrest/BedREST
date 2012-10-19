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

use BedRest\Rest\Configuration;
use BedRest\Rest\Request;
use BedRest\Rest\Response;
use BedRest\Service\Event;
use BedRest\Service\ServiceManager;
use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Events\EventManager;

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
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

    /**
     * Service manager instance.
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * Event manager instance.
     * @var \BedRest\Events\EventManager
     */
    protected $eventManager;

    /**
     * The resource metadata factory.
     * @var \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;

    /**
     * Constructor.
     * @param \BedRest\Rest\Configuration     $configuration
     * @param \BedRest\Events\EventManager    $eventManager
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function __construct(
        Configuration $configuration,
        EventManager $eventManager,
        ServiceManager $serviceManager
    ) {
        $this->configuration = $configuration;
        $this->eventManager = $eventManager;
        $this->serviceManager = $serviceManager;

        $this->resourceMetadataFactory = new ResourceMetadataFactory($configuration);
    }

    /**
     * Returns the configuration object.
     * @return \BedRest\Rest\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the service manager instance.
     * @return \BedRest\Service\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Returns the event manager.
     * @return \BedRest\Events\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Returns resource metadata for a class.
     * @param  string                                     $className
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getResourceMetadata($className)
    {
        return $this->resourceMetadataFactory->getMetadataFor($className);
    }

    /**
     * Returns resource metadata by resource name.
     * @param  string                                     $name
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getResourceMetadataByName($name)
    {
        return $this->resourceMetadataFactory->getMetadataByResourceName($name);
    }

    /**
     * Returns the resource metadata factory.
     * @return \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    public function getResourceMetadataFactory()
    {
        return $this->resourceMetadataFactory;
    }

    /**
     * Processes a REST request, returning a Response object.
     * @param  \BedRest\Rest\Request   $request
     * @throws \BedRest\Rest\Exception
     * @return \BedRest\Rest\Response
     */
    public function process(Request $request)
    {
        // create an empty response
        $response = new Response($this->configuration);

        // establish the best content type
        $contentType = $request->getAcceptBestMatch($this->configuration->getContentTypes());

        if (!$contentType) {
            throw Exception::notAcceptable();
        }

        $response->setContentType($contentType);

        // get service handler
        // TODO: this should be pulled in from the configuration
        $handler = new \BedRest\Resource\Handler\SimpleDoctrineHandler($this);
        $handler->setServiceManager($this->serviceManager);

        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $handler->handleGetResource($request, $response);
                break;
            case Request::METHOD_GET_COLLECTION:
                $handler->handleGetCollection($request, $response);
                break;
            // TODO: implement other methods
            default:
                throw Exception::methodNotAllowed();
                break;
        }

        return $response;
    }
}
