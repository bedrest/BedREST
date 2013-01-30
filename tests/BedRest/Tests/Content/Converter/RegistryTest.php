<?php

namespace BedRest\Tests\Content\Converter;

use BedRest\Tests\BaseTestCase;
use BedRest\Content\Converter\Registry;

/**
 * RegistryTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RegistryTest extends BaseTestCase
{
    /**
     * Default set of converters.
     * @var array
     */
    protected $defaultConverters = array();

    protected function setUp()
    {
        $this->defaultConverters = Registry::getConverters();
    }

    protected function tearDown()
    {
        Registry::setConverters($this->defaultConverters);
    }

    public function testContentConverters()
    {
        $converters = array(
            'application/json' => 'Test1',
            'text/xml' => 'Test2'
        );

        Registry::setConverters($converters);

        $this->assertEquals($converters, Registry::getConverters());

        $this->assertEquals('Test1', Registry::getConverterClass('application/json'));
        $this->assertEquals('Test2', Registry::getConverterClass('text/xml'));

        $this->assertNull(Registry::getConverterClass('text/plain'));
    }
}
