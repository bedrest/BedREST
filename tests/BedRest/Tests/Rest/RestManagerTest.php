<?php

namespace BedRest\Tests\Rest;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Rest\Request\Request;
use BedRest\Rest\Request\Type;
use BedRest\Rest\RestManager;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Tests\BaseTestCase;

/**
 * RestManagerTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RestManagerTest extends BaseTestCase
{
    /**
     * RestManager instance under test.
     *
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

    /**
     * Mock BedRest\Rest\Configuration object.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    protected function setUp()
    {
        parent::setUp();

        // config
        $this->configuration = $this->getMock('BedRest\Rest\Configuration');
        $this->configuration
            ->expects($this->any())
            ->method('getContentTypes')
            ->will($this->returnValue(array('application/json')));

        // object under test
        $this->restManager = new RestManager($this->configuration);
    }

    /**
     * Gets a mock BedRest\Resource\Mapping\ResourceMetadataFactory object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockResourceMetadataFactory()
    {
        // resource meta
        $testResourceMetadata = new ResourceMetadata('testResource');
        $testResourceMetadata->setName('testResource');
        $testResourceMetadata->setService('testService');

        // resource metadata factory
        $resourceMetadataFactory = $this->getMock(
            'BedRest\Resource\Mapping\ResourceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        $resourceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataByResourceName')
            ->with($this->equalTo('testResource'))
            ->will($this->returnValue($testResourceMetadata));

        $resourceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->with($this->equalTo('testResource'))
            ->will($this->returnValue($testResourceMetadata));

        return $resourceMetadataFactory;
    }

    /**
     * Gets a mock BedRest\Service\ServiceManager object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockServiceManager()
    {
        // service manager
        $serviceManager = $this->getMock('BedRest\Service\ServiceManager', array(), array(), '', false);

        return $serviceManager;
    }

    public function testConfiguration()
    {
        $this->assertEquals($this->configuration, $this->restManager->getConfiguration());
    }

    public function testServiceManager()
    {
        $serviceManager = $this->getMockServiceManager();
        $this->restManager->setServiceManager($serviceManager);

        $this->assertEquals($serviceManager, $this->restManager->getServiceManager());
    }

    public function testResourceMetadata()
    {
        $factory = $this->getMockResourceMetadataFactory();
        $this->restManager->setResourceMetadataFactory($factory);

        $meta = $factory->getMetadataFor('testResource');

        $this->assertEquals($meta, $this->restManager->getResourceMetadata($meta->getClassName()));
        $this->assertEquals($meta, $this->restManager->getResourceMetadataByName($meta->getName()));
    }

    public function testResourceMetadataFactory()
    {
        $factory = $this->getMockResourceMetadataFactory();
        $this->restManager->setResourceMetadataFactory($factory);

        $this->assertEquals($factory, $this->restManager->getResourceMetadataFactory());
    }

    public function testAppropriateServiceListenerCalled()
    {
        $this->restManager->setResourceMetadataFactory($this->getMockResourceMetadataFactory());

        // service metadata
        $serviceMetadata = new ServiceMetadata('testService');
        $serviceMetadata->setAllListeners(
            array(
                'GET'               => array('get'),
                'GET_COLLECTION'    => array('getCollection'),
                'POST'              => array('post'),
                'POST_COLLECTION'   => array('postCollection'),
                'PUT'               => array('put'),
                'PUT_COLLECTION'    => array('putCollection'),
                'DELETE'            => array('delete'),
                'DELETE_COLLECTION' => array('deleteCollection')
            )
        );

        // service
        $service = $this->getMock('BedRest\TestFixtures\Services\Company\Generic');
        $service
            ->expects($this->once())
            ->method('get');

        $service
            ->expects($this->once())
            ->method('getCollection');

        $service
            ->expects($this->once())
            ->method('put');

        $service
            ->expects($this->once())
            ->method('putCollection');

        $service
            ->expects($this->once())
            ->method('post');

        $service
            ->expects($this->once())
            ->method('postCollection');

        $service
            ->expects($this->once())
            ->method('delete');

        $service
            ->expects($this->once())
            ->method('deleteCollection');

        // data mapper
        $dataMapper = $this->getMock('BedRest\Service\Data\Mapper');

        $serviceManager = $this->getMockServiceManager();
        $serviceManager
            ->expects($this->any())
            ->method('getServiceMetadata')
            ->with('testService')
            ->will($this->returnValue($serviceMetadata));

        $serviceManager
            ->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($service));

        $serviceManager
            ->expects($this->any())
            ->method('getDataMapper')
            ->will($this->returnValue($dataMapper));

        $this->restManager->setServiceManager($serviceManager);

        // form a basic request object, enough to get RestManager to process it correctly
        $request = new Request();
        $request->setAccept('application/json');
        $request->setResource('testResource');

        // test GET resource
        $request->setMethod(Type::METHOD_GET);
        $this->restManager->process($request);

        // test GET collection
        $request->setMethod(Type::METHOD_GET_COLLECTION);
        $this->restManager->process($request);

        // test POST resource
        $request->setMethod(Type::METHOD_POST);
        $this->restManager->process($request);

        // test POST collection
        $request->setMethod(Type::METHOD_POST_COLLECTION);
        $this->restManager->process($request);

        // test PUT resource
        $request->setMethod(Type::METHOD_PUT);
        $this->restManager->process($request);

        // test PUT collection
        $request->setMethod(Type::METHOD_PUT_COLLECTION);
        $this->restManager->process($request);

        // test DELETE resource
        $request->setMethod(Type::METHOD_DELETE);
        $this->restManager->process($request);

        // test DELETE collection
        $request->setMethod(Type::METHOD_DELETE_COLLECTION);
        $this->restManager->process($request);
    }
}
