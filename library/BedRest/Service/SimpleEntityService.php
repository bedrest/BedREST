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

use BedRest\Rest\RestManager;
use BedRest\Service\Mapping\ResourceMetadata;
use BedRest\Service\Mapping\Annotation as BedRest;

/**
 * SimpleEntityService
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class SimpleEntityService
{
    /**
     * EntityManager instance.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * RestManager instance.
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

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
     * @param \BedRest\Rest\RestManager $rm
     * @param \BedRest\Resource\Mapping\ResourceMetadata $resourceMetadata
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
     */
    public function index()
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

        return $data;
    }

    public function getTotal()
    {
        $query = $this->entityManager->createQuery("SELECT COUNT(r) FROM {$this->resourceClassName} r");
        $result = $query->execute(array(), \Doctrine\ORM\Query::HYDRATE_SINGLE_SCALAR);

        return (int) $result;
    }

    /**
     * Retrieves a single resource entity.
     */
    public function get()
    {
        $entity = $this->entityManager->find($this->resourceClassName, $event->getIdentifier());

        return $entity;
    }
}

