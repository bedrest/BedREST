<?php

namespace BedRest\Tests\Service\Mapping;

use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Service\Mapping\ServiceMetadataFactory;
use BedRest\Service\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use BedRest\TestFixtures\Mocks\TestCache;
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
     * Class under test.
     * @var \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = $this->createFactory();
    }

    /**
     * Creates a fresh instance of the ServiceMetadataFactory
     * @return \BedRest\Service\Mapping\ServiceMetadataFactory
     */
    protected function createFactory()
    {
        $configuration = self::getServiceConfiguration();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Services/Company'
            )
        );

        $configuration->setServiceMetadataDriverImpl($driver);

        return new ServiceMetadataFactory($configuration);
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
        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }

    public function testCache()
    {
        $factory1 = $this->factory;
        $factory2 = $this->createFactory();

        $cache = new TestCache();
        $factory1->setCache($cache);
        $factory2->setCache($cache);

        // test the getter/setter works
        $this->assertEquals($cache, $factory1->getCache());

        // make sure our cache is clean
        $cacheStats = $cache->getStats();
        $this->assertEquals(0, $cacheStats['hits']);
        $this->assertEquals(0, $cacheStats['misses']);

        // get some metadata with the first factory, which should perform a cache miss then store
        // the the new metadata item in the cache
        $factory1->getMetadataFor('BedRest\TestFixtures\Services\Company\Employee');

        $cacheStats = $cache->getStats();
        $this->assertEquals(0, $cacheStats['hits']);
        $this->assertEquals(1, $cacheStats['misses']);

        // now try getting the same metadata with the second factory - it should be cached already
        $factory2->getMetadataFor('BedRest\TestFixtures\Services\Company\Employee');

        $cacheStats = $cache->getStats();
        $this->assertEquals(1, $cacheStats['hits']);
        $this->assertEquals(1, $cacheStats['misses']);

        // make sure we only have one item in the cache
        $this->assertCount(1, $cache->getCacheData());
    }
}
