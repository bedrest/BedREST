<?php

namespace BedRest\Tests\Service;

use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Service\ServiceManager;
use BedRest\Tests\FunctionalModelTestCase;

/**
 * ServiceManagerTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @todo Re-work these tests into true unit tests.
 */
class ServiceManagerTest extends FunctionalModelTestCase
{
    /**w
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceManager = new ServiceManager($this->getServiceConfiguration());
    }

    public function testConfiguration()
    {
        $this->assertEquals($this->getServiceConfiguration(), $this->serviceManager->getConfiguration());
    }

    public function testServiceMetadataFactory()
    {
        $this->assertInstanceOf(
            'BedRest\Service\Mapping\ServiceMetadataFactory',
            $this->serviceManager->getServiceMetadataFactory()
        );
    }

    public function testGetServiceFresh()
    {
        $rmf = $this->getResourceMetadataFactory();

        $service = $this->serviceManager->getService($rmf->getMetadataByResourceName('employee'));
        $serviceDuplicate = $this->serviceManager->getService($rmf->getMetadataByResourceName('employee'));

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
