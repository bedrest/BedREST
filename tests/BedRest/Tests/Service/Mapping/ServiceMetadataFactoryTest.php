<?php

namespace BedRest\Tests\Service\Mapping;

use BedRest\Service\Mapping\ServiceMetadataFactory;
use BedRest\Service\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
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
        $configuration = self::getConfiguration();

        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);

        $configuration->setServiceMetadataDriverImpl($this->driver);

        $this->factory = new ServiceMetadataFactory($configuration);
    }

    public function testGetMetadata()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Services\Company\Employee');

        $this->assertInstanceOf('BedRest\Service\Mapping\ServiceMetadata', $meta);
    }

    public function testGetMetadataInvalid()
    {
        $this->setExpectedException('BedRest\Service\Mapping\Exception');

        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Services\InvalidService');
    }

    public function testGetAllMetadata()
    {
        $this->driver->addPath(TESTS_BASEDIR . '/BedRest/TestFixtures/Services/Company');

        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }

    public function testListenersPopulated()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Services\Company\Employee');

        $eventOne = $meta->getListeners('eventOne');
        $this->assertInternalType('array', $eventOne);
        $this->assertCount(1, $eventOne);
        $this->assertContains('listenerOne', $eventOne);

        $eventTwo = $meta->getListeners('eventTwo');
        $this->assertInternalType('array', $eventTwo);
        $this->assertCount(2, $eventTwo);
        $this->assertContains('listenerOne', $eventTwo);
        $this->assertContains('listenerTwo', $eventTwo);

        $eventThree = $meta->getListeners('eventThree');
        $this->assertInternalType('array', $eventThree);
        $this->assertCount(0, $eventThree);

        $allListeners = array(
            'eventOne' => array(
                'listenerOne'
            ),
            'eventTwo' => array(
                'listenerOne',
                'listenerTwo'
            )
        );
        $this->assertEquals($allListeners, $meta->getAllListeners());
    }
}
