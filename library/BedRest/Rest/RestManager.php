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

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Rest\Configuration;
use BedRest\Rest\Request;
use BedRest\Rest\RequestType;
use BedRest\Rest\Response;
use BedRest\Service\ServiceManager;

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
     * The resource metadata factory.
     * @var \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;

    /**
     * Constructor.
     * @param  \BedRest\Rest\Configuration $configuration
     * @return \BedRest\Rest\RestManager
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;

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
     * Sets the ServiceManager instance to use for service instantiation and configuration.
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns the ServiceManager instance.
     * @return \BedRest\Service\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
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
     * Creates and prepares a new Response object, using the supplied Request object where needed.
     * @param  \BedRest\Rest\Request  $request
     * @return \BedRest\Rest\Response
     */
    protected function createResponse(Request $request)
    {
        // create an empty response
        $response = new Response($this->configuration);

        // establish the best content type
        $contentType = $request->getAccept()->getBestMatch($this->configuration->getContentTypes());

        if (!$contentType) {
            throw Exception::notAcceptable();
        }

        $response->setContentType($contentType);

        return $response;
    }

    /**
     * Processes a REST request, returning a Response object.
     * @param  \BedRest\Rest\Request   $request
     * @throws \BedRest\Rest\Exception
     * @return \BedRest\Rest\Response
     */
    public function process(Request $request)
    {
        $response = $this->createResponse($request);

        // get resource handler for the specified resource
        $resourceMetadata = $this->getResourceMetadataByName($request->getResource());
        $handler = $this->getResourceHandler($resourceMetadata);

        // handle the request
        switch ($request->getMethod()) {
            case RequestType::METHOD_GET:
                $handler->handleGetResource($request, $response);
                break;
            case RequestType::METHOD_GET_COLLECTION:
                $handler->handleGetCollection($request, $response);
                break;
            case RequestType::METHOD_POST:
                $handler->handlePostResource($request, $response);
                break;
            case RequestType::METHOD_POST_COLLECTION:
                $handler->handlePostCollection($request, $response);
                break;
            case RequestType::METHOD_PUT:
                $handler->handlePutResource($request, $response);
                break;
            case RequestType::METHOD_PUT_COLLECTION:
                $handler->handlePutCollection($request, $response);
                break;
            case RequestType::METHOD_DELETE:
                $handler->handleDeleteResource($request, $response);
                break;
            case RequestType::METHOD_DELETE_COLLECTION:
                $handler->handleDeleteCollection($request, $response);
                break;
            default:
                throw Exception::methodNotAllowed();
                break;
        }

        return $response;
    }

    /**
     * Creates and returns the handler for a particular resource.
     * @param  \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     * @return \BedRest\Resource\Handler\Handler
     */
    protected function getResourceHandler(ResourceMetadata $resourceMetadata)
    {
        $handlerClass = $resourceMetadata->getHandler();
        $handler = new $handlerClass($this);

        return $handler;
    }
}
