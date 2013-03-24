<?php

namespace BedRest\Tests\Service;

use BedRest\Service\ServiceManager;
use BedRest\Tests\FunctionalModelTestCase;

/**
 * ServiceManagerTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceManagerTest extends FunctionalModelTestCase
{
    /**w
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    protected function setUp()
    {
        parent::setUp();
        
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setServiceMetadataFactory($this->getServiceMetadataFactory());
        $this->serviceManager->setServiceContainer($this->getServiceContainer());
    }
    
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

    public function testServiceMetadataFactory()
    {
        $factory = $this->getMockServiceMetadataFactory();
        $this->serviceManager->setServiceMetadataFactory($factory);
        
        $this->assertEquals($factory, $this->serviceManager->getServiceMetadataFactory());
    }
    
    public function testServiceContainer()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->serviceManager->setServiceContainer($container);
        
        $this->assertEquals($container, $this->serviceManager->getServiceContainer());
    }

    public function testGetServiceFresh()
    {
        $rmf = $this->getResourceMetadataFactory();
        $resourceMeta = $rmf->getMetadataByResourceName('employee');

        $service = $this->serviceManager->getService($resourceMeta);
        $serviceDuplicate = $this->serviceManager->getService($resourceMeta);

        $this->assertInstanceOf('BedRest\TestFixtures\Services\Company\Employee', $service);
        $this->assertEquals(spl_object_hash($service), spl_object_hash($serviceDuplicate));
    }

    public function testGetServiceExistingForDifferentResource()
    {
        $rmf = $this->getResourceMetadataFactory();
        $rmAsset = $rmf->getMetadataByResourceName('asset');
        $rmDepartment = $rmf->getMetadataByResourceName('department');

        $serviceAsset = $this->serviceManager->getService($rmAsset);
        $serviceDepartment = $this->serviceManager->getService($rmDepartment);

        $this->assertInstanceOf('BedRest\TestFixtures\Services\Company\Generic', $serviceAsset);
        $this->assertInstanceOf('BedRest\TestFixtures\Services\Company\Generic', $serviceDepartment);
        $this->assertNotEquals(spl_object_hash($serviceAsset), spl_object_hash($serviceDepartment));
    }

    public function testGetMapperFresh()
    {
        $mapper = $this->serviceManager->getDataMapper('BedRest\TestFixtures\Services\Company\Employee');
        $this->assertInstanceOf('BedRest\Model\Doctrine\Mapper', $mapper);

        $mapperDuplicate = $this->serviceManager->getDataMapper('BedRest\TestFixtures\Services\Company\Employee');
        $this->assertEquals($mapper, $mapperDuplicate);
    }

    public function testGetMapperForNonExistentService()
    {
        $this->setExpectedException('BedRest\Service\Exception');
        $this->serviceManager->getDataMapper('NonExistentServiceClass');
    }

    public function testGetMapperForNonService()
    {
        $this->setExpectedException('BedRest\Service\Exception');
        $this->serviceManager->getDataMapper('BedRest\TestFixtures\Services\InvalidService');
    }
}
