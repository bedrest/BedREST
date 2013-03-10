<?php

namespace BedRest\Tests\Rest;

use BedRest\Tests\BaseTestCase;
use BedRest\Rest\Configuration;

/**
 * ConfigurationTest
 *
 * Tests BedRest\Rest\Configuration
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ConfigurationTest extends BaseTestCase
{
    public function testContentTypes()
    {
        $config = new Configuration();
        $contentTypes = array(
            'application/json',
            'text/xml'
        );

        $config->setContentTypes($contentTypes);

        $this->assertEquals($contentTypes, $config->getContentTypes());
    }

    public function testDefaultService()
    {
        $config = new Configuration();
        $config->setDefaultService('Services\DefaultService');

        $this->assertEquals('Services\DefaultService', $config->getDefaultService());
    }

    public function testResourcePaths()
    {
        $config = new Configuration();

        $this->assertEmpty($config->getResourcePaths());

        $paths = array(
            'test1',
            'test2'
        );
        $config->setResourcePaths($paths);

        $this->assertEquals($paths, $config->getResourcePaths());
    }

    public function testMetadataDriver()
    {
        $config = new Configuration();
        $driver = $this->getMock('BedRest\Resource\Mapping\Driver\Driver');

        $config->setResourceMetadataDriverImpl($driver);
        $this->assertEquals($driver, $config->getResourceMetadataDriverImpl());
    }

    public function testMetadataCache()
    {
        $config = new Configuration();
        $cache = $this->getMock('Doctrine\Common\Cache\Cache');

        $config->setResourceMetadataCacheImpl($cache);
        $this->assertEquals($cache, $config->getResourceMetadataCacheImpl());
    }
}
