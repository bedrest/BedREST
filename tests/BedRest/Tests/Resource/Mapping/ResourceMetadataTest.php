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
