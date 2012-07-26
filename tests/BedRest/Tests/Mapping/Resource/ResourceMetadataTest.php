<?php

namespace BedRest\Tests\Mapping\Resource;

use BedRest\Tests\BaseTestCase;
use BedRest\Mapping\Resource\ResourceMetadata;

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

        $this->assertEquals('Entity\Test', $rm->getEntityClass());
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

