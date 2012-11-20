<?php

namespace BedRest\Tests\Content\Negotiation;

use BedRest\Content\Negotiation\MediaTypeList;
use BedRest\Tests\BaseTestCase;

/**
 * MediaTypeListTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class MediaTypeListTest extends BaseTestCase
{
    public function testSingleItem()
    {
        $mediaTypeList = new MediaTypeList('application/json');

        $expected = array(
            array(
                'media_range' => 'application/json',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $mediaTypeList->getMediaTypes());
    }

    public function testMultipleItems()
    {
        $mediaTypeList = new MediaTypeList('application/json, text/xml');

        $expected = array(
            array(
                'media_range' => 'application/json',
                'q' => 1
            ),
            array(
                'media_range' => 'text/xml',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $mediaTypeList->getMediaTypes());
    }

    public function testMultipleItemQualityOrdering()
    {
        $mediaTypeList = new MediaTypeList('text/xml;q=0.5, application/json;q=1');

        $expected = array(
            array(
                'media_range' => 'application/json',
                'q' => 1
            ),
            array(
                'media_range' => 'text/xml',
                'q' => 0.5
            )
        );

        $this->assertEquals($expected, $mediaTypeList->getMediaTypes());
    }

    public function testBestMatch()
    {
        $mediaTypeList = new MediaTypeList('application/json;q=1, text/xml;q=0.5');

        $bestMatch = $mediaTypeList->getBestMatch(
            array(
                'application/json',
                'text/xml'
            )
        );

        $this->assertEquals('application/json', $bestMatch);
    }

    public function testBestMatchNoMatch()
    {
        $mediaTypeList = new MediaTypeList('application/json');

        $bestMatch = $mediaTypeList->getBestMatch(
            array(
                'text/xml'
            )
        );

        $this->assertFalse($bestMatch);
    }
}
