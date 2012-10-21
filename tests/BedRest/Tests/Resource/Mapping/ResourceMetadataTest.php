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
}
