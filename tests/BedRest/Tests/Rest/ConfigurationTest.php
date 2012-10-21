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
            'application/json',
            'text/xml'
        );

        $config->setContentTypes($contentTypes);

        $this->assertEquals($contentTypes, $config->getContentTypes());
    }

    public function testContentConverters()
    {
        $config = new Configuration();
        $converters = array(
            'application/json' => 'Test1',
            'text/xml' => 'Test2'
        );

        $config->setContentConverters($converters);

        $this->assertEquals($converters, $config->getContentConverters());

        $this->assertEquals('Test1', $config->getContentConverter('application/json'));
        $this->assertEquals('Test2', $config->getContentConverter('text/xml'));

        $this->assertNull($config->getContentConverter('text/plain'));
    }

    public function testServiceNamespaces()
    {
        $config = new Configuration();
        $namespaces = array(
            'setOne' => 'Services\SetOne\\',
            'setTwo' => 'Services\SetTwo'
        );
        $normalisedNamespaces = array(
            'setOne' => 'Services\SetOne\\',
            'setTwo' => 'Services\SetTwo\\'
        );

        $config->setServiceNamespaces($namespaces);

        $this->assertEquals($normalisedNamespaces, $config->getServiceNamespaces());

        $this->assertEquals('Services\SetOne\\', $config->getServiceNamespace('setOne'));
        $this->assertEquals('Services\SetTwo\\', $config->getServiceNamespace('setTwo'));

        $this->assertNull($config->getServiceNamespace('setThree'));

        $config->addServiceNamespace('setThree', 'Services\SetThree\\');

        $this->assertEquals('Services\SetThree\\', $config->getServiceNamespace('setThree'));
    }

    public function testDefaultResourceHandler()
    {
        $config = new Configuration();
        $config->setDefaultResourceHandler('ResourceHandlers\DefaultHandler');

        $this->assertEquals('ResourceHandlers\DefaultHandler', $config->getDefaultResourceHandler());
    }

    public function testDefaultService()
    {
        $config = new Configuration();
        $config->setDefaultService('Services\DefaultService');

        $this->assertEquals('Services\DefaultService', $config->getDefaultService());
    }
}
