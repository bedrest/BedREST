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

namespace BedRest\Resource\Handler;

use BedRest\Rest\Request;
use BedRest\Rest\ResourceNotFoundException;
use BedRest\Rest\Response;
use BedRest\Rest\RestManager;
use BedRest\Service\ServiceManager;
use BedRest\Service\Data\SimpleDoctrineMapper;

/**
 * SimpleDoctrineHandler
 *
 * Author: Geoff Adams <geoff@dianode.net>
 */
class SimpleDoctrineHandler implements Handler
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
     * @return \BedRest\Service\Data\DataMapper
     */
    public function getDataMapper()
    {
        // TODO: the choice of data mapper should be in configuration somewhere
        $className = 'BedRest\Service\Data\SimpleDoctrineMapper';

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
     * Handles GET requests on single resources.
     * @param  \BedRest\Rest\Request                   $request
     * @param  \BedRest\Rest\Response                  $response
     * @throws \BedRest\Rest\ResourceNotFoundException
     * @return void
     */
    public function handleGetResource(Request $request, Response $response)
    {
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        // get the service
        $service = $this->serviceManager->getService($resourceMetadata);

        $dataMapper = $this->getDataMapper();

        $identifier = $request->getRouteComponent('identifier');
        $data = $service->get($identifier);

        // TODO: strengthen this check
        if ($data === null) {
            throw new ResourceNotFoundException;
        }

        $response->setBody($dataMapper->reverse($data));
    }

    /**
     * Handles GET requests on collections of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetCollection(Request $request, Response $response)
    {
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        // get the service
        $service = $this->serviceManager->getService($resourceMetadata);

        $dataMapper = $this->getDataMapper();

        $data = $service->getCollection();

        $response->setBody($dataMapper->reverse($data));
    }

    /**
     * Handles POST requests on single resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePostResource(Request $request, Response $response)
    {
        // create an empty instance of the resource entity
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        $className = $resourceMetadata->getClassName();
        $resource = new $className;

        // populate the resource with data from the request using a DataMapper
        $requestData = (array) $request->getPayload();

        $dataMapper = $this->getDataMapper();
        $dataMapper->map($resource, $requestData);

        // perform the actual service operation
        $service = $this->serviceManager->getService($resourceMetadata);

        $service->create($resource);

        // set the response with the content of the new resource entity
        $response->setBody($dataMapper->reverse($resource));
    }

    /**
     * Handles PUT requests on single resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePutResource(Request $request, Response $response)
    {
        throw new \RuntimeException('PUT resource has not been implemented yet.');
    }

    /**
     * Handles DELETE requests on single resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleDeleteResource(Request $request, Response $response)
    {
        throw new \RuntimeException('DELETE resource has not been implemented yet.');
    }
}
