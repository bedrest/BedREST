<?php

namespace BedRest\Tests\Service\Mapping;

use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Service\Mapping\ServiceMetadataFactory;
use BedRest\Service\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use BedRest\TestFixtures\Services\Company\Employee as EmployeeService;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * ServiceMetadataFactoryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadataFactoryTest extends BaseTestCase
{
    /**
     * Driver for the factory.
     * @var \BedRest\Service\Mapping\Driver\AnnotationDriver
     */
    protected $driver;

    /**
     * Class under test.
     * @var \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        $configuration = self::getServiceConfiguration();

        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);

        $configuration->setServiceMetadataDriverImpl($this->driver);

        $this->factory = new ServiceMetadataFactory($configuration);
    }

    public function testGetMetadata()
    {
        $entity = new EmployeeService;

        $meta = $this->factory->getMetadataFor(get_class($entity));
        $this->assertInstanceOf('BedRest\Service\Mapping\ServiceMetadata', $meta);

        $expectedMeta = $entity->getMetadata();
        $this->assertEquals($expectedMeta['className'], $meta->getClassName());
        $this->assertEquals($expectedMeta['type'], $meta->getType());
        $this->assertEquals($expectedMeta['listeners']['eventOne'], $meta->getListeners('eventOne'));
        $this->assertEquals($expectedMeta['listeners']['eventTwo'], $meta->getListeners('eventTwo'));
    }

    public function testGetMetadataInvalid()
    {
        $this->setExpectedException('BedRest\Service\Mapping\Exception');

        $this->factory->getMetadataFor('BedRest\TestFixtures\Services\InvalidService');
    }

    public function testGetAllMetadata()
    {
        $this->driver->addPath(TESTS_BASEDIR . '/BedRest/TestFixtures/Services/Company');

        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }
}
