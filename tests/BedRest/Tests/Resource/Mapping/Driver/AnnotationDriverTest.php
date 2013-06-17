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

namespace BedRest\Tests\Resource\Mapping\Driver;

use BedRest\Resource\Mapping\ResourceMetadata;
use BedRest\Resource\Mapping\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * AnnotationDriverTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriverTest extends BaseTestCase
{
    /**
     * @var \BedRest\Resource\Mapping\Driver\AnnotationDriver;
     */
    protected $driver;

    protected function setUp()
    {
        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('BedRest\Resource\Mapping\Driver\Driver', $this->driver);
    }

    public function testPaths()
    {
        $this->assertEmpty($this->driver->getPaths());

        $this->driver->addPath('test1');
        $this->assertEquals(array('test1'), $this->driver->getPaths());

        $this->driver->addPaths(array('test2', 'test3'));
        $this->assertEquals(array('test1', 'test2', 'test3'), $this->driver->getPaths());
    }

    public function testNoPathsThrowsException()
    {
        $this->setExpectedException('\BedRest\Resource\Mapping\Exception');

        $this->driver->getAllClassNames();
    }

    public function testInvalidPathThrowsException()
    {
        $this->setExpectedException('\BedRest\Resource\Mapping\Exception');

        $this->driver->addPath('/tmp/this/is/an/invalid/path');
        $this->driver->getAllClassNames();
    }

    public function testIsResource()
    {
        $this->assertTrue($this->driver->isResource('BedRest\TestFixtures\Models\Company\Employee'));
    }

    public function testImplicitResourceNameAutoGeneration()
    {
        $this->driver->addPath(TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company/');

        $modelClass = 'BedRest\TestFixtures\Models\Company\Employee';
        $rm = new ResourceMetadata($modelClass);
        $this->driver->loadMetadataForClass($modelClass, $rm);

        $this->assertEquals('employee', $rm->getName());
    }

    public function testSubResources()
    {
        $this->driver->addPath(TESTS_BASEDIR . '/BedRest/TestFixtures/Models/Company/');

        $modelClass = 'BedRest\TestFixtures\Models\Company\Employee';
        $rm = new ResourceMetadata($modelClass);
        $this->driver->loadMetadataForClass($modelClass, $rm);

        $subResources = array(
            'assets' => array(
                'fieldName' => 'Assets',
                'service'   => 'EmployeeAssetsService'
            )
        );
        $this->assertEquals($subResources, $rm->getSubResources());
    }
}
