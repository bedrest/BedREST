<?php

namespace BedRest\Tests\Mapping\Resource\Driver;

use BedRest\Mapping\Resource\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * AnnotationDriverTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class AnnotationDriverTest extends BaseTestCase
{
    protected $driver;
    
    protected function setUp()
    {
        $reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($reader);
    }
    
    public function testInterface()
    {
        $this->assertInstanceOf('BedRest\Mapping\Resource\Driver\Driver', $this->driver);
    }
    
    public function testIsResource()
    {
        $this->assertTrue($this->driver->isResource('BedRest\TestFixtures\Models\Company\Employee'));
    }
}
