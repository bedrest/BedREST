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

namespace BedRest\Model\Doctrine;

use BedRest\Resource\Handler\Handler as HandlerInterface;
use BedRest\Rest\Request;
use BedRest\Rest\ResourceNotFoundException;
use BedRest\Rest\Response;
use BedRest\Rest\RestManager;
use BedRest\Service\ServiceManager;

/**
 * Handler
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Handler implements HandlerInterface
{
    /**
     * ServiceManager instance.
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * RestManager instance.
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

    /**
     * Constructor.
     * @param \BedRest\Rest\RestManager $restManager
     */
    public function __construct(RestManager $restManager)
    {
        $this->restManager = $restManager;

        $this->serviceManager = new ServiceManager($restManager->getServiceConfiguration());
    }

    /**
     * Returns the ServiceManager instance used by the handler.
     * @return \BedRest\Service\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Returns the DataMapper to be used by the handler.
     * @return \BedRest\Service\Data\Mapper
     */
    public function getDataMapper()
    {
        // TODO: the choice of data mapper should be in configuration somewhere
        $className = 'BedRest\Service\Data\Mapper';

        // instantiate
        $id = "{$className}";
        $container = $this->restManager->getServiceConfiguration()->getServiceContainer();

        if (!$container->hasDefinition($id)) {
            $container->register($id, $className)
                ->addArgument($this->restManager->getServiceConfiguration())
                ->addArgument($this->serviceManager)
                ->addMethodCall('setEntityManager', array('%doctrine.entityManager%'));
        }

        return $container->get($id);
    }

    /**
     * Handles a GET request for a single resource.
     * @param  \BedRest\Rest\Request                   $request
     * @param  \BedRest\Rest\Response                  $response
     * @throws \BedRest\Rest\ResourceNotFoundException
     * @return void
     */
    public function handleGetResource(Request $request, Response $response)
    {
        // get the parameters
        $depth = (int) $request->getParameter('depth', 1);

        // get the service and request the collection
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        $service = $this->serviceManager->getService($resourceMetadata);

        $identifier = $request->getRouteComponent('identifier');
        $data = $service->get($identifier);

        // TODO: strengthen this check
        if ($data === null) {
            throw new ResourceNotFoundException;
        }

        // get the data mapper and compose the response body
        $dataMapper = $this->getDataMapper();

        $response->setBody($dataMapper->reverse($data, $depth));
    }

    /**
     * Handles a GET request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetCollection(Request $request, Response $response)
    {
        // get the parameters
        $limit = (int) $request->getParameter('maxResults', 10);
        $offset = 0;
        $depth = (int) $request->getParameter('depth', 1);

        // get the service and request the collection
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        $service = $this->serviceManager->getService($resourceMetadata);

        $data = $service->getCollection(array(), array(), $limit, $offset);

        // get the data mapper and compose the response body
        $dataMapper = $this->getDataMapper();

        $response->setBody($dataMapper->reverse($data, $depth));
    }

    /**
     * Handles a POST request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePostResource(Request $request, Response $response)
    {
        // get the parameters
        $depth = (int) $request->getParameter('depth', 1);

        // create an empty instance of the resource entity
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        $className = $resourceMetadata->getClassName();
        $resource = new $className;

        // populate the resource with data from the request using a DataMapper
        $requestData = (array) $request->getBody();

        $dataMapper = $this->getDataMapper();
        $dataMapper->map($resource, $requestData);

        // perform the actual service operation
        $service = $this->serviceManager->getService($resourceMetadata);

        $service->create($resource);

        // set the response with the content of the new resource entity
        $response->setBody($dataMapper->reverse($resource, $depth));
    }

    /**
     * Handles a POST request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePostCollection(Request $request, Response $response)
    {
        // get the parameters
        $depth = (int) $request->getParameter('depth', 1);

        // create an empty instance of the resource entity
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());
        $className = $resourceMetadata->getClassName();

        // get the service instance and DataMapper
        $dataMapper = $this->getDataMapper();
        $service = $this->serviceManager->getService($resourceMetadata);

        // loop through each item in the collection provided in the request
        $requestData = (array) $request->getBody();

        $resources = array();

        foreach ($requestData as $item) {
            $resource = new $className;

            // populate the resource with data from the request using the DataMapper
            $dataMapper->map($resource, $requestData);

            // perform the actual service operation
            $service->create($resource);

            $resources[] = $resource;
        }

        // set the response with the content of the new resource entity
        $response->setBody($dataMapper->reverse($resources, $depth));
    }

    /**
     * Handles a PUT request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePutResource(Request $request, Response $response)
    {
        // get the parameters
        $depth = (int) $request->getParameter('depth', 1);

        // get the metadata for the resource and the service required
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());
        $service = $this->serviceManager->getService($resourceMetadata);

        // get the instance referred to
        $identifier = $request->getRouteComponent('identifier');
        $resource = $service->get($identifier);

        // populate the resource with data from the request using a DataMapper
        $requestData = (array) $request->getBody();

        $dataMapper = $this->getDataMapper();
        $dataMapper->map($resource, $requestData);

        // perform the actual service operation
        $service->update($resource);

        // set the response with the content of the new resource entity
        $response->setBody($dataMapper->reverse($resource, $depth));
    }

    /**
     * Handles a PUT request for a collection of resources.
     * @param  \BedRest\Rest\Request   $request
     * @param  \BedRest\Rest\Response  $response
     * @throws \BedRest\Rest\Exception
     * @return void
     */
    public function handlePutCollection(Request $request, Response $response)
    {
        throw new \BedRest\Rest\Exception("PUT requests to resource collections are not supported at this time.");
    }

    /**
     * Handles a DELETE request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleDeleteResource(Request $request, Response $response)
    {
        // get the service
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        $service = $this->serviceManager->getService($resourceMetadata);

        // retrieve the resource and check it exists
        $identifier = $request->getRouteComponent('identifier');
        $resource = $service->get($identifier);

        if ($resource === null) {
            throw new ResourceNotFoundException;
        }

        // delete the requested item
        $service->delete($resource);

        // populate the response
        $data = array(
            'deleted' => true
        );

        $response->setBody($data);
    }

    /**
     * Handles a DELETE request for a collection of resources.
     * @param  \BedRest\Rest\Request   $request
     * @param  \BedRest\Rest\Response  $response
     * @throws \BedRest\Rest\Exception
     * @return void
     */
    public function handleDeleteCollection(Request $request, Response $response)
    {
        throw new \BedRest\Rest\Exception("DELETE requests to resource collections are not supported at this time.");
    }
}
