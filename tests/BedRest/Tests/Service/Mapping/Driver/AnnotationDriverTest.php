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

namespace BedRest\Tests\Service\Mapping\Driver;

use BedRest\Service\Mapping\Driver\AnnotationDriver;
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
     * Driver under test.
     * @var \BedRest\Service\Mapping\Driver\AnnotationDriver
     */
    protected $driver;

    protected function setUp()
    {
        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('BedRest\Service\Mapping\Driver\Driver', $this->driver);
    }

    public function testIsService()
    {
        $this->assertTrue($this->driver->isService('BedRest\TestFixtures\Services\Company\Employee'));
    }

    public function testAddPath()
    {
        $initialCount = count($this->driver->getPaths());

        $this->driver->addPath('testpath');
        $paths = $this->driver->getPaths();

        $this->assertEquals($initialCount + 1, count($this->driver->getPaths()));
        $this->assertContains('testpath', $paths);
    }

    public function testAddPaths()
    {
        $initialCount = count($this->driver->getPaths());

        $this->driver->addPaths(array('testpath', 'testpath2'));
        $paths = $this->driver->getPaths();

        $this->assertEquals($initialCount + 2, count($this->driver->getPaths()));

        $this->assertContains('testpath', $paths);
        $this->assertContains('testpath2', $paths);
    }

    public function testLoadMetadataForInvalidServiceThrowsException()
    {
        $this->setExpectedException('BedRest\Service\Mapping\Exception');

        $sm = $this->getMock('BedRest\Service\Mapping\ServiceMetadata', array(), array(), '', false);
        $this->driver->loadMetadataForClass('BedRest\TestFixtures\Services\InvalidService', $sm);
    }
}
