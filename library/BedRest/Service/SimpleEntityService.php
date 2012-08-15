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

use BedRest\RestManager;
use BedRest\Event;
use BedRest\Mapping\Resource\ResourceMetadata;
use BedRest\Mapping\Service\Annotation as BedRest;

/**
 * SimpleEntityService
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class SimpleEntityService implements Service
{
    /**
     * EntityManager instance.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * RestManager instance.
     * @var \BedRest\RestManager
     */
    protected $restManager;

    /**
     * Resource metadata.
     * @var \BedRest\Mapping\Resource\ResourceMetadata
     */
    protected $resourceMetadata;

    /**
     * Resource class name
     * @var string
     */
    protected $resourceClassName;

    /**
     * Constructor.
     * @param \BedRest\RestManager $rm
     * @param \BedRest\Mapping\Resource\ResourceMetadata $resourceMetadata
     */
    public function __construct(RestManager $rm, ResourceMetadata $resourceMetadata)
    {
        $this->restManager = $rm;
        $this->entityManager = $rm->getConfiguration()->getEntityManager();

        $this->resourceMetadata = $resourceMetadata;
        $this->resourceClassName = $resourceMetadata->getClassName();
    }

    /**
     * Retrieves a collection of resource entities.
     * @param \BedRest\Event\GetCollectionEvent $event
     *
     * @BedRest\Listener(event="getCollection")
     */
    public function index(Event\GetCollectionEvent $event)
    {
        $offset = 0;
        $limit = 10;

        $query = $this->entityManager->createQuery("SELECT r FROM {$this->resourceClassName} r");
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        $collection = $query->execute();

        $total = $this->getTotal();

        $data = array(
            'items' => count($collection) ? $collection : array(),
            'count' => count($collection),
            'total' => $total,
            'perPage' => $limit
        );

        $event->getResponse()->setBody($data);
    }

    public function getTotal()
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(r) FROM {$this->resourceClassName} r");
        $result = $query->execute(array(), \Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        return (int) $result;
    }

    /**
     * Retrieves a single resource entity.
     * @param \BedRest\Event\GetEntityEvent $event
     *
     * @BedRest\Listener(event="getEntity")
     */
    public function get(Event\GetEntityEvent $event)
    {
        $entity = $this->entityManager->find($this->resourceClassName, $event->getIdentifier());

        $event->getResponse()->setBody($entity);
    }
}

