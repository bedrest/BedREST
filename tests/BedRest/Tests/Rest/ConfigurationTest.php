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
    public function testGetNullEntityManager()
    {
        $config = new Configuration();

        $this->assertEquals(null, $config->getEntityManager());
    }

    public function testSetEntityManager()
    {
        $config = new Configuration();

        $em = $this->getEntityManager();

        $config->setEntityManager($em);

        $this->assertEquals($em, $config->getEntityManager());
    }

    public function testContentTypes()
    {
        $config = new Configuration();
        $contentTypes = array(
            'application/json' => 'Test1',
            'text/xml' => 'Test2'
        );

        $config->setContentTypes($contentTypes);

        $this->assertEquals($contentTypes, $config->getContentTypes());
        $this->assertEquals('Test1', $config->getContentConverter('application/json'));
        $this->assertEquals('Test2', $config->getContentConverter('text/xml'));

        $this->assertNull($config->getContentConverter('text/plain'));

        $config->addContentType('text/plain', 'Test3');

        $this->assertEquals('Test3', $config->getContentConverter('text/plain'));
    }
}
