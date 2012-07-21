<?php

namespace BedRest\Tests\Mapping;

use BedRest\Tests\BaseTestCase;
use BedRest\Mapping\ResourceMetadata;

/**
 * ResourceMetadataTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataTest extends BaseTestCase
{
    public function testName()
    {
        $rm = new ResourceMetadata('Test');
        
        $this->assertEquals('Test', $rm->getName());
    }
    
    public function testServiceClass()
    {
        $rm = new ResourceMetadata('Test');
        $rm->setServiceClass('Services\Test');
        
        $this->assertEquals('Services\Test', $rm->getServiceClass());
    }
}
