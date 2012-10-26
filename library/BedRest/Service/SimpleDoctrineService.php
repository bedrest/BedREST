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

namespace BedRest\Service;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Service\Mapping\Annotation as BedRest;
use Doctrine\ORM\EntityManager;

/**
 * SimpleDoctrineService
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class SimpleDoctrineService
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
     * Constructor.
     * @param \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
     */
    public function __construct(ResourceMetadata $resourceMetadata)
    {
        $this->resourceMetadata = $resourceMetadata;
        $this->resourceClassName = $resourceMetadata->getClassName();
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
     * Retrieves a collection of resource entities.
     * @return array
     */
    public function getCollection()
    {
        $offset = 0;
        $limit = 10;

        $query = $this->entityManager->createQuery("SELECT r FROM {$this->resourceClassName} r");
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        $collection = $query->execute();

        $total = $this->getCollectionSize();

        $data = array(
            'items' => count($collection) ? $collection : array(),
            'count' => count($collection),
            'total' => $total,
            'perPage' => $limit
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
     * Retrieves a single resource entity.
     * @param  mixed  $identifier
     * @return object
     */
    public function get($identifier)
    {
        $entity = $this->entityManager->find($this->resourceClassName, $identifier);

        return $entity;
    }

    /**
     * Creates a single resource entity.
     * @param  mixed $entity
     * @return mixed
     */
    public function create($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
