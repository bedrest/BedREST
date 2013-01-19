<?php

namespace BedRest\Tests\Rest;

use BedRest\Rest\Request;
use BedRest\Rest\RequestType;
use BedRest\Rest\RestManager;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use BedRest\TestFixtures\ResourceHandlers\DefaultHandler;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * RestManagerTest
 *
 * Author: Geoff Adams <geoff@dianode.net>
 */
class RestManagerTest extends BaseTestCase
{
    /**
     * RestManager instance under test.
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

    protected function setUp()
    {
        $config = self::getConfiguration();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models',
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company'
            )
        );

        $config->setResourceMetadataDriverImpl($driver);

        $this->restManager = new RestManager($config);
    }

    public function testConfiguration()
    {
        $this->assertEquals(self::getConfiguration(), $this->restManager->getConfiguration());
    }

    public function testServiceConfiguration()
    {
        $this->restManager->setServiceConfiguration(self::getServiceConfiguration());

        $this->assertEquals(self::getServiceConfiguration(), $this->restManager->getServiceConfiguration());
    }

    public function testResourceMetadata()
    {
        $resourceName = 'employee';
        $resourceClass = 'BedRest\TestFixtures\Models\Company\Employee';

        // retrieval by class name
        $meta = $this->restManager->getResourceMetadata($resourceClass);

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);
        $this->assertEquals($resourceClass, $meta->getClassName());
        $this->assertEquals($resourceName, $meta->getName());

        // retrieval by name
        $meta = $this->restManager->getResourceMetadataByName('employee');

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);
        $this->assertEquals($resourceClass, $meta->getClassName());
        $this->assertEquals($resourceName, $meta->getName());
    }

    public function testResourceMetadataFactory()
    {
        $factory = $this->restManager->getResourceMetadataFactory();

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadataFactory', $factory);
    }

    public function testAppropriateHandlerMethodCalled()
    {
        $request = new Request(self::getConfiguration());
        $request->setAccept('application/json');
        $request->setResource('employee');

        // test GET resource
        $request->setMethod(RequestType::METHOD_GET);

        $this->assertEquals(0, DefaultHandler::$handleGetResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handleGetResourceCalled);

        // test GET collection
        $request->setMethod(RequestType::METHOD_GET_COLLECTION);

        $this->assertEquals(0, DefaultHandler::$handleGetCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handleGetCollectionCalled);

        // test POST resource
        $request->setMethod(RequestType::METHOD_POST);

        $this->assertEquals(0, DefaultHandler::$handlePostResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handlePostResourceCalled);

        // test POST collection
        $request->setMethod(RequestType::METHOD_POST_COLLECTION);

        $this->assertEquals(0, DefaultHandler::$handlePostCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handlePostCollectionCalled);

        // test PUT resource
        $request->setMethod(RequestType::METHOD_PUT);

        $this->assertEquals(0, DefaultHandler::$handlePutResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handlePutResourceCalled);

        // test PUT collection
        $request->setMethod(RequestType::METHOD_PUT_COLLECTION);

        $this->assertEquals(0, DefaultHandler::$handlePutCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handlePutCollectionCalled);

        // test DELETE resource
        $request->setMethod(RequestType::METHOD_DELETE);

        $this->assertEquals(0, DefaultHandler::$handleDeleteResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handleDeleteResourceCalled);

        // test DELETE collection
        $request->setMethod(RequestType::METHOD_DELETE_COLLECTION);

        $this->assertEquals(0, DefaultHandler::$handleDeleteCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, DefaultHandler::$handleDeleteCollectionCalled);
    }
}
