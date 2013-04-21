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
}
