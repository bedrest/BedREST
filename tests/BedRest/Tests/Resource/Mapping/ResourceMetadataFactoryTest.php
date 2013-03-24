<?php

namespace BedRest\Tests\Resource\Mapping;

use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\TestFixtures\Mocks\TestCache;
use BedRest\Tests\FunctionalModelTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * ResourceMetadataFactoryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @todo This is a functional test suite, can be refactored to be a true unit test.
 * @todo Remove references to $this->getConfiguration()
 */
class ResourceMetadataFactoryTest extends FunctionalModelTestCase
{
    /**
     * Class under test.
     *
     * @var \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = $this->createFactory();
    }

    /**
     * Creates a fresh instance of the ResourceMetadataFactory.
     *
     * @return \BedRest\Resource\Mapping\ResourceMetadataFactory
     */
    protected function createFactory()
    {
        $configuration = $this->getConfiguration();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models',
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company'
            )
        );

        return new ResourceMetadataFactory($configuration, $driver);
    }

    public function testGetMetadata()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Models\Company\Employee');

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);

        $this->assertEquals('employee', $meta->getName());
        $this->assertEquals('BedRest\TestFixtures\Services\Company\Employee', $meta->getService());
    }

    public function testGetMetadataInvalid()
    {
        $this->setExpectedException('BedRest\Resource\Mapping\Exception');

        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Models\InvalidResource');
    }

    public function testGetMetadataByResourceName()
    {
        $meta = $this->factory->getMetadataByResourceName('employee');

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);

        $this->assertEquals('employee', $meta->getName());
        $this->assertEquals('BedRest\TestFixtures\Services\Company\Employee', $meta->getService());
    }

    public function testGetMetadataByInvalidResourceName()
    {
        $this->setExpectedException('BedRest\Resource\Mapping\Exception');

        $meta = $this->factory->getMetadataByResourceName('nonexistant');
    }

    public function testGetAllMetadata()
    {
        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }

    public function testGetMetadataWithDefaultValues()
    {
        $config = $this->getConfiguration();
        $meta = $this->factory->getMetadataByResourceName('defaults');

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);

        $this->assertEquals('defaults', $meta->getName());
        $this->assertEquals($config->getDefaultService(), $meta->getService());
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
        $factory1->getMetadataFor('BedRest\TestFixtures\Models\Company\Employee');

        $cacheStats = $cache->getStats();
        $this->assertEquals(0, $cacheStats['hits']);
        $this->assertEquals(1, $cacheStats['misses']);

        // now try getting the same metadata with the second factory - it should be cached already
        $factory2->getMetadataFor('BedRest\TestFixtures\Models\Company\Employee');

        $cacheStats = $cache->getStats();
        $this->assertEquals(1, $cacheStats['hits']);
        $this->assertEquals(1, $cacheStats['misses']);

        // make sure we only have one item in the cache
        $this->assertCount(1, $cache->getCacheData());
    }
}
