<?php

namespace BedRest\Tests\Mapping\Resource;

use BedRest\Tests\BaseTestCase;
use BedRest\Mapping\Resource\ResourceMetadataFactory;
use BedRest\Mapping\Resource\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * ResourceMetadataFactoryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResourceMetadataFactoryTest extends BaseTestCase
{
    /**
     * Class under test.
     * @var BedRest\Mapping\Resource\ResourceMetadataFactory
     */
    protected $factory;
    
    protected function setUp()
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        
        $this->factory = new ResourceMetadataFactory();
        $this->factory->setClassMetadataFactory(self::getEntityManager()->getMetadataFactory());
        $this->factory->setMetadataDriver($driver);
    }
    
    public function testGetMetadata()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Models\Company\Employee');
        
        $this->assertEquals('employee', $meta->getName());
        $this->assertEquals('BedRest\TestFixtures\Services\Company\Employee', $meta->getServiceClass());
    }
}
