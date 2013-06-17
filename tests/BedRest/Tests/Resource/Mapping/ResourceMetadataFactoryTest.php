<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\Tests\Resource\Mapping;

use BedRest\Resource\Mapping\ResourceMetadataFactory;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\TestFixtures\Mocks\TestCache;
use BedRest\Tests\BaseTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * ResourceMetadataFactoryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactoryTest extends BaseTestCase
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
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models',
                TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company'
            )
        );

        return new ResourceMetadataFactory($driver);
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
        $meta = $this->factory->getMetadataByResourceName('defaults');

        $this->assertInstanceOf('BedRest\Resource\Mapping\ResourceMetadata', $meta);

        $this->assertEquals('defaults', $meta->getName());
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
