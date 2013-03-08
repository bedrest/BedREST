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

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Rest\ResourceNotFoundException;
use BedRest\Rest\Request\Request;
use BedRest\Service\Data\Mapper as MapperInterface;
use BedRest\Service\Mapping\Annotation as BedRest;
use Doctrine\ORM\EntityManager;

/**
 * Service
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class Service
{
    /**
     * EntityManager instance.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Resource metadata.
     * @var \BedRest\Resource\Mapping\ResourceMetadata
     */
    protected $resourceMetadata;

    /**
     * Resource class name
     * @var string
     */
    protected $resourceClassName;

    /**
     * DataMapper instance.
     * @var \BedRest\Service\Data\Mapper
     */
    protected $dataMapper;

    /**
     * Constructor.
     * @param \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     */
    public function __construct(ResourceMetadata $resourceMetadata, MapperInterface $dataMapper)
    {
        $this->resourceMetadata = $resourceMetadata;
        $this->resourceClassName = $resourceMetadata->getClassName();
        $this->dataMapper = $dataMapper;
    }

    /**
     * Sets the EntityManager instance.
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieves a single resource entity.
     * @param  \BedRest\Rest\Request\Request $request
     * @return object
     *
     * @BedRest\Listener(event="GET")
     */
    public function get(Request $request)
    {
        $identifier = $request->getRouteComponent('identifier');

        $resource = $this->entityManager->find($this->resourceClassName, $identifier);

        if ($resource === null) {
            throw new ResourceNotFoundException;
        }

        return $resource;
    }

    /**
     * Retrieves a collection of resource entities.
     * @param  \BedRest\Rest\Request\Request $request
     * @return array
     *
     * @BedRest\Listener(event="GET_COLLECTION")
     */
    public function getCollection(Request $request)
    {
        // get the parameters
        $limit = (int) $request->getParameter('maxResults', 10);
        $offset = 0;
        $depth = (int) $request->getParameter('depth', 1);

        // compose a Query object
        $query = $this->entityManager->createQuery("SELECT r FROM {$this->resourceClassName} r");
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        // get the data and some metadata
        $collection = $query->execute();

        $total = $this->getCollectionSize();

        $data = array(
            'items' => count($collection) ? $collection : array(),
            'count' => count($collection),
            'total' => (int) $total,
            'perPage' => (int) $limit
        );

        return $data;
    }

    /**
     * Retrieves the size of a collection.
     * @return int
     */
    public function getCollectionSize()
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(r) FROM {$this->resourceClassName} r");
        $result = $query->execute(array(), \Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        return (int) $result;
    }

    /**
     * Creates a single resource entity.
     * @param  \BedRest\Rest\Request\Request $request
     * @return object
     *
     * @BedRest\Listener(event="POST")
     */
    public function create(Request $request)
    {
        $resource = new $this->resourceClassName;

        // populate the resource with data from the request using a DataMapper
        $requestData = (array) $request->getBody();

        $this->dataMapper->map($resource, $requestData);

        // persist
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        return $resource;
    }

    /**
     * Updates a single resource entity.
     * @param  \BedRest\Rest\Request\Request $request
     * @return object
     *
     * @BedRest\Listener(event="PUT")
     */
    public function update(Request $request)
    {
        // get the existing resource
        $identifier = $request->getRouteComponent('identifier');
        $resource = $this->entityManager->find($this->resourceClassName, $identifier);

        // populate the resource with data from the request using a DataMapper
        $requestData = (array) $request->getBody();

        $this->dataMapper->map($resource, $requestData);

        // persist
        $this->entityManager->persist($resource);
        $this->entityManager->flush();

        return $resource;
    }

    /**
     * Deletes an entity, referenced by an identifier.
     * @param  \BedRest\Rest\Request\Request $request
     * @return array
     *
     * @BedRest\Listener(event="DELETE")
     */
    public function delete(Request $request)
    {
        // retrieve the resource and check it exists
        $identifier = $request->getRouteComponent('identifier');
        $resource = $this->get($identifier);

        if ($resource === null) {
            throw new ResourceNotFoundException;
        }

        // remove
        $this->entityManager->remove($resource);
        $this->entityManager->flush();

        // populate the response
        return array(
            'deleted' => true
        );
    }
}
