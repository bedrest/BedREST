<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\Tests\Content\Negotiation;

use BedRest\Content\Negotiation\Negotiator;
use BedRest\Tests\BaseTestCase;

/**
 * NegotiatorTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class NegotiatorTest extends BaseTestCase
{
    /**
     * @var \BedRest\Content\Negotiation\Negotiator
     */
    protected $negotiator;

    protected function setUp()
    {
        $this->negotiator = new Negotiator();
    }

    public function testSupportedMediaTypes()
    {
        $supportedMediaTypes = array(
            'text/xml'          => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy',
            'application/json'  => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $this->assertEquals($supportedMediaTypes, $this->negotiator->getSupportedMediaTypes());
    }

    public function invalidSupportedMediaTypes()
    {
        return array(
            array(
                array(
                    'text/xml'
                )
            ),
            array(
                array(
                    'stdClass'
                )
            ),
            array(
                array(
                    1 => 'text/xml'
                )
            ),
            array(
                array(
                    'text/xml' => false
                )
            ),
            array(
                array(
                    'text/xml' => 'stdClass',
                    'application/json'
                )
            )
        );
    }

    /**
     * @dataProvider invalidSupportedMediaTypes
     */
    public function testSupportedMediaTypesWithInvalidMappingThrowsException($value)
    {
        $this->setExpectedException('BedRest\Content\Negotiation\Exception');

        $this->negotiator->setSupportedMediaTypes($value);
    }

    public function testBestMatchIsTakenFromMediaTypeList()
    {
        $supportedMediaTypes = array(
            'application/json'  => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with(array_keys($supportedMediaTypes))
            ->will($this->returnValue('application/json'));

        $result = $this->negotiator->negotiate(null, $mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
    }

    public function testSupportedMediaTypesUsedForBestMatchResolution()
    {
        $supportedMediaTypes = array(
            'text/xml'          => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy',
            'application/json'  => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with(array_keys($supportedMediaTypes))
            ->will($this->returnValue('application/json'));

        $result = $this->negotiator->negotiate(null, $mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
    }

    public function testNoBestMatchThrowsException()
    {
        $supportedMediaTypes = array(
            'text/xml' => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with(array_keys($supportedMediaTypes))
            ->will($this->returnValue(false));

        $this->setExpectedException('BedRest\Content\Negotiation\Exception');
        $this->negotiator->negotiate(null, $mediaTypeList);
    }

    public function testContentIsConvertedToBestMatch()
    {
        $supportedMediaTypes = array(
            'application/json' => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        // mock media type list
        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with(array_keys($supportedMediaTypes))
            ->will($this->returnValue('application/json'));

        $content = 'raw_data';

        $result = $this->negotiator->negotiate($content, $mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
        $this->assertEquals('encoded_data', $result->content);
    }

    public function testEncodeUsesCorrectConverter()
    {
        $supportedMediaTypes = array(
            'application/json' => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $result = $this->negotiator->encode('raw_data', 'application/json');
        $this->assertEquals('encoded_data', $result);
    }

    public function testDecodeUsesCorrectConverter()
    {
        $supportedMediaTypes = array(
            'application/json' => 'BedRest\TestFixtures\Mocks\Content\Converter\Dummy'
        );

        $this->negotiator->setSupportedMediaTypes($supportedMediaTypes);

        $result = $this->negotiator->decode('raw_data', 'application/json');
        $this->assertEquals('decoded_data', $result);
    }

    public function testEncodeWithInvalidContentTypeThrowsException()
    {
        $this->setExpectedException('\BedRest\Content\Negotiation\Exception');
        $this->negotiator->encode('raw_data', 'application/json');
    }

    public function testDecodeWithInvalidContentTypeThrowsException()
    {
        $this->setExpectedException('\BedRest\Content\Negotiation\Exception');
        $this->negotiator->decode('raw_data', 'application/json');
    }
}
