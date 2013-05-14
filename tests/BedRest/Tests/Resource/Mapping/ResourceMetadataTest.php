<?php

namespace BedRest\Tests\Resource\Mapping;

use BedRest\Tests\BaseTestCase;
use BedRest\Resource\Mapping\ResourceMetadata;

/**
 * ResourceMetadataTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataTest extends BaseTestCase
{
    public function testClassName()
    {
        $meta = new ResourceMetadata('Resource\Test');
        $this->assertEquals('Resource\Test', $meta->getClassName());

        $meta->setClassName('Resource\TestTwo');
        $this->assertEquals('Resource\TestTwo', $meta->getClassName());
    }

    public function testName()
    {
        $meta = new ResourceMetadata('Resource\Test');

        $meta->setName('Test');
        $this->assertEquals('Test', $meta->getName());
    }

    public function testServiceClass()
    {
        $meta = new ResourceMetadata('Resource\Test');

        $meta->setService('Services\Test');
        $this->assertEquals('Services\Test', $meta->getService());
    }

    public function testSubResources()
    {
        $subResources = array(
            'sub1' => 'assoc1',
            'sub2' => 'assoc2',
        );

        $meta = new ResourceMetadata('Resource\Test');
        $meta->setSubResources($subResources);
        $this->assertEquals($subResources, $meta->getSubResources());

        $this->assertTrue($meta->hasSubResource('sub1'));
        $this->assertFalse($meta->hasSubResource('sub3'));
    }

    public function testSubResourcesEnforcesKeyValueMapping()
    {
        $subResources = array(
            'sub1',
            'sub2',
        );

        $meta = new ResourceMetadata('Resource\Test');

        $this->setExpectedException('BedRest\Resource\Mapping\Exception');
        $meta->setSubResources($subResources);
    }
}
