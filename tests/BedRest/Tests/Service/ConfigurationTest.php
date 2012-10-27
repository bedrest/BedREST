<?php

namespace BedRest\Tests\Service;

use BedRest\Tests\BaseTestCase;
use BedRest\Service\Configuration;

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
}
