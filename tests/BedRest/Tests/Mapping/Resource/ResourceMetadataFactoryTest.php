<?php

namespace BedRest\Tests\Mapping\Resource;

use BedRest\Configuration;
use BedRest\Mapping\Resource\ResourceMetadataFactory;
use BedRest\Mapping\Resource\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
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
        $configuration = self::getConfiguration();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);

        $configuration->setResourceMetadataDriverImpl($driver);
        
        $this->factory = new ResourceMetadataFactory($configuration);
    }

    public function testGetMetadata()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Models\Company\Employee');

        $this->assertInstanceOf('BedRest\Mapping\Resource\ResourceMetadata', $meta);
        
        $this->assertEquals('employee', $meta->getName());
        $this->assertEquals('BedRest\TestFixtures\Services\Company\Employee', $meta->getServiceClass());
    }
    
    public function testGetMetadataInvalid()
    {
        $this->setExpectedException('BedRest\Mapping\MappingException');
        
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Models\InvalidResource');
    }
    
    public function testGetMetadataByResourceName()
    {
        $meta = $this->factory->getMetadataByResourceName('employee');
        
        $this->assertInstanceOf('BedRest\Mapping\Resource\ResourceMetadata', $meta);
        
        $this->assertEquals('employee', $meta->getName());
        $this->assertEquals('BedRest\TestFixtures\Services\Company\Employee', $meta->getServiceClass());
    }
    
    public function testGetMetadataByInvalidResourceName()
    {
        $this->setExpectedException('BedRest\Mapping\MappingException');
        
        $meta = $this->factory->getMetadataByResourceName('nonexistant');
    }

    public function testGetAllMetadata()
    {
        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }
}

