<?php

namespace BedRest\TestFixtures\Services\Company;

use BedRest\Rest\Request\Request;
use BedRest\Service\Mapping\Annotation as BedRest;
use Doctrine\ORM\EntityManager;

/**
 * Generic
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service(type="DOCTRINE", dataMapper="BedRest\TestFixtures\Services\Company\DataMapper\Generic")
 */
class Generic
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Sets the EntityManager instance.
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Returns values for metadata to be compared against in tests.
     * @return array
     */
    public function getGenericMetadata()
    {
        return array(
            'className' => __CLASS__,
            'type' => \BedRest\Service\Mapping\ServiceMetadata::TYPE_BASIC,
            'listeners' => array(
                'GET' => array(
                    'get'
                ),
                'GET_COLLECTION' => array(
                    'getCollection'
                ),
                'POST' => array(
                    'post'
                ),
                'POST_COLLECTION' => array(
                    'postCollection'
                ),
                'PUT' => array(
                    'put'
                ),
                'PUT_COLLECTION' => array(
                    'putCollection'
                ),
                'DELETE' => array(
                    'delete'
                ),
                'DELETE_COLLECTION' => array(
                    'deleteCollection'
                ),
            )
        );
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET")
     */
    public function get(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET_COLLECTION")
     */
    public function getCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST")
     */
    public function post(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST_COLLECTION")
     */
    public function postCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT")
     */
    public function put(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT_COLLECTION")
     */
    public function putCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE")
     */
    public function delete(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE_COLLECTION")
     */
    public function deleteCollection(Request $request)
    {
    }
}
