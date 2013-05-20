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
            'sub1' => array(
                'fieldName' => 'assoc1',
                'service'   => null,
            ),
            'sub2' => array(
                'fieldName' => 'assoc2',
                'service'   => 'sub2Service'
            )
        );

        $meta = new ResourceMetadata('Resource\Test');
        $meta->setSubResources($subResources);
        $this->assertEquals($subResources, $meta->getSubResources());

        $this->assertTrue($meta->hasSubResource('sub1'));
        $this->assertFalse($meta->hasSubResource('sub3'));

        $this->assertEquals($subResources['sub1'], $meta->getSubResource('sub1'));
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

    public function testSubResourcesEnforcesArrayValues()
    {
        $subResources = array(
            'sub1' => 'not_an_array',
        );

        $meta = new ResourceMetadata('Resource\Test');

        $this->setExpectedException('BedRest\Resource\Mapping\Exception');
        $meta->setSubResources($subResources);
    }

    public function testSubResourcesEnforcesFullDataSetForEachEntry()
    {
        $providedSubResources = array(
            'sub1' => array(
                'fieldName' => 'assoc1',
                'service'   => 'sub1Service',
            ),
            'sub2' => array(
                'fieldName' => 'assoc2',
            )
        );

        $expectedSubResources = $providedSubResources;
        $expectedSubResources['sub2']['service'] = null;

        $meta = new ResourceMetadata('Resource\Test');

        $meta->setSubResources($providedSubResources);
        $this->assertEquals($expectedSubResources, $meta->getSubResources());
    }

    public function testSubResourceWithoutFieldNameThrowsException()
    {
        $subResources = array(
            'sub1' => array(
                'fieldName' => 'assoc1',
                'service'   => 'sub1Service',
            ),
            'sub2' => array(
                'service' => 'sub2Service',
            )
        );

        $meta = new ResourceMetadata('Resource\Test');

        $this->setExpectedException('BedRest\Resource\Mapping\Exception');
        $meta->setSubResources($subResources);
    }
}
