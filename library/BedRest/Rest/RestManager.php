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
use BedRest\Rest\Request\Request;
use BedRest\Rest\Response\Response;
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
     *
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

    /**
     * Service manager instance.
     *
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * The resource metadata factory.
     *
     * @var \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected $resourceMetadataFactory;

    /**
     * Constructor.
     *
     * @param \BedRest\Rest\Configuration $configuration
     *
     * @return \BedRest\Rest\RestManager
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Returns the configuration object.
     *
     * @return \BedRest\Rest\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Sets the ServiceManager instance to use for service instantiation and configuration.
     *
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Returns the ServiceManager instance.
     *
     * @return \BedRest\Service\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
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
     * Returns the ResourceMetadataFactory used for retrieving resource metadata.
     *
     * @return \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    public function getResourceMetadataFactory()
    {
        return $this->resourceMetadataFactory;
    }

    /**
     * Returns resource metadata for a class.
     *
     * @param  string                                     $className
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getResourceMetadata($className)
    {
        return $this->resourceMetadataFactory->getMetadataFor($className);
    }

    /**
     * Returns resource metadata by resource name.
     *
     * @param  string                                     $name
     * @return \BedRest\Resource\Mapping\ResourceMetadata
     */
    public function getResourceMetadataByName($name)
    {
        return $this->resourceMetadataFactory->getMetadataByResourceName($name);
    }

    /**
     * Creates and prepares a new Response object, using the supplied Request object where needed.
     *
     * @param  \BedRest\Rest\Request\Request   $request
     * @return \BedRest\Rest\Response\Response
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
     *
     * @param  \BedRest\Rest\Request\Request   $request
     * @throws \BedRest\Rest\Exception
     * @return \BedRest\Rest\Response\Response
     */
    public function process(Request $request)
    {
        $response = $this->createResponse($request);

        // get information about the requested resource
        $resourceMetadata = $this->getResourceMetadataByName($request->getResource());

        // get the service and data mapper
        $serviceManager = $this->getServiceManager();
        $serviceMetadata = $serviceManager->getServiceMetadata($resourceMetadata->getService());
        $service = $serviceManager->getService($resourceMetadata);
        $dataMapper = $serviceManager->getDataMapper($resourceMetadata->getService());

        // handle the request
        $listeners = $serviceMetadata->getListeners($request->getMethod());

        $data = array();
        foreach ($listeners as $listener) {
            $data = $service->$listener($request);
        }

        if (count($data) === 1) {
            $data = reset($data);
        }

        // compose the response body to desired depth
        $mapDepth = (int) $request->getParameter('depth', 1);
        $response->setBody($dataMapper->reverse($data, $mapDepth));

        // TODO: generate additional response information (ETag, Cache-Control etc)
        return $response;
    }
}
