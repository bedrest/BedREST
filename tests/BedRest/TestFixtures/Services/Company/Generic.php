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
 * @BedRest\Service(type="DOCTRINE", dataMapper="BedRest\Model\Doctrine\Mapper")
 */
class Generic
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public static $handleGetResourceCalled = 0;

    public static $handleGetCollectionCalled = 0;

    public static $handlePostResourceCalled = 0;

    public static $handlePostCollectionCalled = 0;

    public static $handlePutResourceCalled = 0;

    public static $handlePutCollectionCalled = 0;

    public static $handleDeleteResourceCalled = 0;

    public static $handleDeleteCollectionCalled = 0;

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
                    'handleGetResource'
                ),
                'GET_COLLECTION' => array(
                    'handleGetCollectionResource'
                ),
                'POST' => array(
                    'handlePostResource'
                ),
                'POST_COLLECTION' => array(
                    'handlePostCollectionResource'
                ),
                'PUT' => array(
                    'handlePutResource'
                ),
                'PUT_COLLECTION' => array(
                    'handlePutCollectionResource'
                ),
                'DELETE' => array(
                    'handleDeleteResource'
                ),
                'DELETE_COLLECTION' => array(
                    'handleDeleteCollectionResource'
                ),
            )
        );
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET")
     */
    public function handleGetResource(Request $request)
    {
        self::$handleGetResourceCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET_COLLECTION")
     */
    public function handleGetCollection(Request $request)
    {
        self::$handleGetCollectionCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST")
     */
    public function handlePostResource(Request $request)
    {
        self::$handlePostResourceCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST_COLLECTION")
     */
    public function handlePostCollection(Request $request)
    {
        self::$handlePostCollectionCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT")
     */
    public function handlePutResource(Request $request)
    {
        self::$handlePutResourceCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT_COLLECTION")
     */
    public function handlePutCollection(Request $request)
    {
        self::$handlePutCollectionCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE")
     */
    public function handleDeleteResource(Request $request)
    {
        self::$handleDeleteResourceCalled++;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE_COLLECTION")
     */
    public function handleDeleteCollection(Request $request)
    {
        self::$handleDeleteCollectionCalled++;
    }
}
