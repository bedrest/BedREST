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
    }

    /**
     * Sets the ServiceManager instance to be used by the handler.
     * @param \BedRest\Service\ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
        // TODO: this should be pulled in from the configuration
        return new SimpleDoctrineMapper($this->restManager->getConfiguration(), $this->serviceManager);
    }

    /**
     * Handles GET requests on single entities.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetResource(Request $request, Response $response)
    {
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        // get the service
        // TODO: injection of dependencies should happen in the ServiceManager, perhaps using config
        $service = $this->serviceManager->getService($resourceMetadata);
        $service->setEntityManager($this->restManager->getConfiguration()->getEntityManager());

        $dataMapper = $this->getDataMapper();

        $identifier = $request->getRouteComponent('identifier');
        $data = $service->get($identifier);

        $response->setBody($dataMapper->reverse($data));
    }

    /**
     * Handles GET requests on collections of entities.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetCollection(Request $request, Response $response)
    {
        $resourceMetadata = $this->restManager->getResourceMetadataByName($request->getResource());

        // get the service
        // TODO: injection of dependencies should happen in the ServiceManager
        $service = $this->serviceManager->getService($resourceMetadata);
        $service->setEntityManager($this->restManager->getConfiguration()->getEntityManager());

        $dataMapper = $this->getDataMapper();

        $data = $service->getCollection();

        $response->setBody($dataMapper->reverse($data));
    }
}
