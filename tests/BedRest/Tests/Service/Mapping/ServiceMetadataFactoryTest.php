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

namespace BedRest\Tests\Service\Mapping;

use BedRest\Service\Mapping\ServiceMetadataFactory;
use BedRest\Service\Mapping\Driver\AnnotationDriver;
use BedRest\TestFixtures\Mocks\TestCache;
use BedRest\TestFixtures\Services\Company\Employee as EmployeeService;
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
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths(
            array(
                TESTS_BASEDIR . '/BedRest/TestFixtures/Services/Company'
            )
        );

        return new ServiceMetadataFactory($driver);
    }

    public function testGetMetadata()
    {
        $service = new EmployeeService;

        $meta = $this->factory->getMetadataFor(get_class($service));
        $this->assertInstanceOf('BedRest\Service\Mapping\ServiceMetadata', $meta);

        $expectedMeta = $service->getMetadata();
        $this->assertEquals($expectedMeta['className'], $meta->getClassName());
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

    public function testInheritance()
    {
        $service = new EmployeeService;

        $meta = $this->factory->getMetadataFor(get_class($service));
        $this->assertInstanceOf('BedRest\Service\Mapping\ServiceMetadata', $meta);

        $baseMeta = $service->getGenericMetadata();
        $this->assertEquals($baseMeta['listeners']['GET'], $meta->getListeners('GET'));
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
