<?php

namespace BedRest\Tests\Service;

use BedRest\Service\ServiceManager;
use BedRest\Tests\RequiresModelTestCase;

/**
 * ServiceManagerTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceManagerTest extends RequiresModelTestCase
{
    /**w
     * @var \BedRest\Service\ServiceManager
     */
    protected $serviceManager;

    protected function setUp()
    {
        $this->serviceManager = new ServiceManager(static::getServiceConfiguration());
    }

    public function testConfiguration()
    {
        $this->assertEquals(static::getServiceConfiguration(), $this->serviceManager->getConfiguration());
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
        $rmf = new \BedRest\Resource\Mapping\ResourceMetadataFactory(self::getConfiguration());

        $service = $this->serviceManager->getService($rmf->getMetadataByResourceName('employee'));
        $this->assertInstanceOf('BedRest\TestFixtures\Services\Company\Employee', $service);
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $service->getEntityManager());

        $serviceDuplicate = $this->serviceManager->getService($rmf->getMetadataByResourceName('employee'));
        $this->assertEquals($service, $serviceDuplicate);
    }

    public function testGetServiceExistingForDifferentResource()
    {

    }

    public function testGetMapperFresh()
    {

    }

    public function testGetMapperExisting()
    {

    }
}
