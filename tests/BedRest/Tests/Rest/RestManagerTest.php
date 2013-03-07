<?php

namespace BedRest\Tests\Rest;

use BedRest\Rest\Request\Request;
use BedRest\Rest\Request\Type;
use BedRest\Rest\RestManager;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\Service\ServiceManager;
use BedRest\Tests\BaseTestCase;
use BedRest\TestFixtures\Services\Company\Employee as EmployeeService;
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
        $serviceManager = new ServiceManager(self::getServiceConfiguration());
        $this->restManager->setServiceManager($serviceManager);
    }

    public function testConfiguration()
    {
        $this->assertEquals(self::getConfiguration(), $this->restManager->getConfiguration());
    }

    public function testServiceManager()
    {
        $serviceManager = new ServiceManager(self::getServiceConfiguration());
        $this->restManager->setServiceManager($serviceManager);

        $this->assertEquals($serviceManager, $this->restManager->getServiceManager());
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

    public function testAppropriateServiceListenerCalled()
    {
        $request = new Request(self::getConfiguration());
        $request->setAccept('application/json');
        $request->setResource('employee');

        // test GET resource
        $request->setMethod(Type::METHOD_GET);

        $this->assertEquals(0, EmployeeService::$handleGetResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handleGetResourceCalled);

        // test GET collection
        $request->setMethod(Type::METHOD_GET_COLLECTION);

        $this->assertEquals(0, EmployeeService::$handleGetCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handleGetCollectionCalled);

        // test POST resource
        $request->setMethod(Type::METHOD_POST);

        $this->assertEquals(0, EmployeeService::$handlePostResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handlePostResourceCalled);

        // test POST collection
        $request->setMethod(Type::METHOD_POST_COLLECTION);

        $this->assertEquals(0, EmployeeService::$handlePostCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handlePostCollectionCalled);

        // test PUT resource
        $request->setMethod(Type::METHOD_PUT);

        $this->assertEquals(0, EmployeeService::$handlePutResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handlePutResourceCalled);

        // test PUT collection
        $request->setMethod(Type::METHOD_PUT_COLLECTION);

        $this->assertEquals(0, EmployeeService::$handlePutCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handlePutCollectionCalled);

        // test DELETE resource
        $request->setMethod(Type::METHOD_DELETE);

        $this->assertEquals(0, EmployeeService::$handleDeleteResourceCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handleDeleteResourceCalled);

        // test DELETE collection
        $request->setMethod(Type::METHOD_DELETE_COLLECTION);

        $this->assertEquals(0, EmployeeService::$handleDeleteCollectionCalled);
        $this->restManager->process($request);
        $this->assertEquals(1, EmployeeService::$handleDeleteCollectionCalled);
    }
}
