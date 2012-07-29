<?php

namespace BedRest\Tests\Mapping\Service;

use BedRest\Mapping\Service\ServiceMetadataFactory;
use BedRest\Mapping\Service\Driver\AnnotationDriver;
use BedRest\Tests\BaseTestCase;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * ServiceMetadataFactoryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadataFactoryTest extends BaseTestCase
{
    /**
     * Class under test.
     * @var BedRest\Mapping\Service\ServiceMetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        $configuration = self::getConfiguration();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);

        $configuration->setServiceMetadataDriverImpl($driver);
        
        $this->factory = new ServiceMetadataFactory($configuration);
    }

    public function testGetMetadata()
    {
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Services\Company\Employee');

        $this->assertInstanceOf('BedRest\Mapping\Service\ServiceMetadata', $meta);
    }
    
    public function testGetMetadataInvalid()
    {
        $this->setExpectedException('BedRest\Mapping\MappingException');
        
        $meta = $this->factory->getMetadataFor('BedRest\TestFixtures\Services\InvalidService');
    }

    public function testGetAllMetadata()
    {
        $metaCollection = $this->factory->getAllMetadata();

        $this->assertInternalType('array', $metaCollection);
        $this->assertGreaterThan(0, count($metaCollection));
    }
}
