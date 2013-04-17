<?php

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
            ->with($supportedMediaTypes)
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
            ->with($supportedMediaTypes)
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
            ->will($this->returnValue('application/json'));
                
        $content = 'raw_data';
        
        $result = $this->negotiator->negotiate($content, $mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
        $this->assertEquals('encoded_data', $result->content);
    }
}
