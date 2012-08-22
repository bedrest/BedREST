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
    public function testEntityClass()
    {
        $rm = new ResourceMetadata('Entity\Test');

        $this->assertEquals('Entity\Test', $rm->getClassName());
    }

    public function testName()
    {
        $rm = new ResourceMetadata('Entity\Test');
        $rm->setName('Test');

        $this->assertEquals('Test', $rm->getName());
    }

    public function testServiceClass()
    {
        $rm = new ResourceMetadata('Entity\Test');
        $rm->setServiceClass('Services\Test');

        $this->assertEquals('Services\Test', $rm->getServiceClass());
    }
}

