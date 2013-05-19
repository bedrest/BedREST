<?php

namespace BedRest\Tests\Rest;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Rest\Dispatcher;
use BedRest\Rest\Request\Request;
use BedRest\Rest\Request\Type;
use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Tests\BaseTestCase;

/**
 * DispatcherTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class DispatcherTest extends BaseTestCase
{
    /**
     * RestManager instance under test.
     *
     * @var \BedRest\Rest\Dispatcher
     */
    protected $dispatcher;

    protected function setUp()
    {
        parent::setUp();

        $this->dispatcher = new Dispatcher();
    }

    /**
     * Gets a mock BedRest\Resource\Mapping\ResourceMetadataFactory object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @todo Remove usage of non-mock classes (ResourceMetadata for instance).
     */
    protected function getMockResourceMetadataFactory()
    {
        $testResourceMetadata = new ResourceMetadata('testResource');
        $testResourceMetadata->setName('testResource');
        $testResourceMetadata->setService('testService');

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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockServiceMetadataFactory()
    {
        $factory = $this->getMock(
            'BedRest\Service\Mapping\ServiceMetadataFactory',
            array(),
            array(),
            '',
            false
        );

        return $factory;
    }

    public function testEventManager()
    {
        $eventManager = $this->getMock('BedRest\Events\EventManager');

        $this->dispatcher->setEventManager($eventManager);
        $this->assertEquals($eventManager, $this->dispatcher->getEventManager());
    }

    public function testResourceMetadata()
    {
        $factory = $this->getMockResourceMetadataFactory();
        $this->dispatcher->setResourceMetadataFactory($factory);

        $meta = $factory->getMetadataFor('testResource');

        $this->assertEquals($meta, $this->dispatcher->getResourceMetadata($meta->getClassName()));
        $this->assertEquals($meta, $this->dispatcher->getResourceMetadataByName($meta->getName()));
    }

    public function testResourceMetadataFactory()
    {
        $factory = $this->getMockResourceMetadataFactory();

        $this->dispatcher->setResourceMetadataFactory($factory);
        $this->assertEquals($factory, $this->dispatcher->getResourceMetadataFactory());
    }

    public function testServiceMetadataFactory()
    {
        $factory = $this->getMockServiceMetadataFactory();

        $this->dispatcher->setServiceMetadataFactory($factory);
        $this->assertEquals($factory, $this->dispatcher->getServiceMetadataFactory());
    }

    public function testServiceLocator()
    {
        $locator = $this->getMock('BedRest\Service\LocatorInterface');

        $this->dispatcher->setServiceLocator($locator);
        $this->assertEquals($locator, $this->dispatcher->getServiceLocator());
    }

    public function requests()
    {
        return array(
            array(Type::METHOD_GET),
            array(Type::METHOD_GET_COLLECTION),
            array(Type::METHOD_POST),
            array(Type::METHOD_POST_COLLECTION),
            array(Type::METHOD_PUT),
            array(Type::METHOD_PUT_COLLECTION),
            array(Type::METHOD_DELETE),
            array(Type::METHOD_DELETE_COLLECTION),
        );
    }

    /**
     * @dataProvider requests
     */
    public function testDispatchCallsCorrectServiceListeners($method)
    {
        $this->dispatcher->setResourceMetadataFactory($this->getMockResourceMetadataFactory());

        // event manager
        $eventManager = $this->getMock('BedRest\Events\EventManager');

        $eventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with($method);

        $eventManager
            ->expects($this->any())
            ->method('addListeners');

        // service metadata
        $serviceMetadata = new ServiceMetadata('testService');
        $serviceMetadata->addListener($method, strtolower($method));

        $serviceMetadataFactory = $this->getMockServiceMetadataFactory();
        $serviceMetadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue($serviceMetadata));

        // service
        $service = $this->getMock('BedRest\TestFixtures\Services\Company\Generic');

        // service locator
        $serviceLocator = $this->getMock('BedRest\Service\LocatorInterface');
        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('testService')
            ->will($this->returnValue($service));

        // configure the RestManager
        $this->dispatcher->setServiceMetadataFactory($serviceMetadataFactory);
        $this->dispatcher->setServiceLocator($serviceLocator);
        $this->dispatcher->setEventManager($eventManager);

        // dispatch the request
        $request = new Request();
        $request->setResource('testResource');
        $request->setMethod($method);

        $this->dispatcher->dispatch($request);
    }
}
