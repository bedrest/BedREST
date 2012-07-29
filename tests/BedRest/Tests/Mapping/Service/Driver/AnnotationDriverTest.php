<?php

namespace BedRest\Tests\Mapping\Service\Driver;

use BedRest\Mapping\Service\Driver\AnnotationDriver;
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
     * @var \BedRest\Mapping\Service\Driver\AnnotationDriver
     */
    protected $driver;
    
    protected function setUp()
    {
        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);
    }
    
    public function testInterface()
    {
        $this->assertInstanceOf('BedRest\Mapping\Service\Driver\Driver', $this->driver);
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
}
