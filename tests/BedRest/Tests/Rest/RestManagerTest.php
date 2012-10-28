<?php

namespace BedRest\Tests\Rest;

use BedRest\Rest\RestManager;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
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
}
