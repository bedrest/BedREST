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

    public function testAcceptedTypes()
    {
        $acceptedTypes = array('text/xml', 'application/json');

        $this->negotiator->setAcceptedTypes($acceptedTypes);

        $this->assertEquals($acceptedTypes, $this->negotiator->getAcceptedTypes());
    }

    public function testBestMatchIsSelected()
    {
        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->will($this->returnValue('application/json'));

        $result = $this->negotiator->negotiate($mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
    }

    public function testBestMatchIsFoundInAcceptedTypes()
    {
        $acceptedTypes = array('text/xml', 'application/json');

        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with($acceptedTypes)
            ->will($this->returnValue('application/json'));

        $this->negotiator->setAcceptedTypes($acceptedTypes);

        $result = $this->negotiator->negotiate($mediaTypeList);

        $this->assertInstanceOf('BedRest\Content\Negotiation\NegotiatedResult', $result);
        $this->assertEquals('application/json', $result->contentType);
    }

    public function testNoMatchIsFoundInAcceptedTypesThrowsException()
    {
        $acceptedTypes = array('text/xml');

        $mediaTypeList = $this->getMock('BedRest\Content\Negotiation\MediaTypeList', array(), array(), '', false);
        $mediaTypeList->expects($this->any())
            ->method('getBestMatch')
            ->with($acceptedTypes)
            ->will($this->returnValue(false));

        $this->negotiator->setAcceptedTypes($acceptedTypes);

        $this->setExpectedException('BedRest\Content\Negotiation\Exception');
        $this->negotiator->negotiate($mediaTypeList);
    }
}
