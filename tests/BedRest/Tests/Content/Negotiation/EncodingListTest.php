<?php

namespace BedRest\Tests\Content\Negotiation;

use BedRest\Content\Negotiation\EncodingList;
use BedRest\Tests\BaseTestCase;

/**
 * EncodingTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class EncodingTest extends BaseTestCase
{
    public function testSingleItem()
    {
        $encodingList = new EncodingList('gzip');

        $expected = array(
            array(
                'encoding' => 'gzip',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $encodingList->getEncodings());
    }

    public function testMultipleItems()
    {
        $encodingList = new EncodingList('gzip, deflate');

        $expected = array(
            array(
                'encoding' => 'gzip',
                'q' => 1
            ),
            array(
                'encoding' => 'deflate',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $encodingList->getEncodings());
    }

    public function testMultipleItemQualityOrdering()
    {
        $encodingList = new EncodingList('deflate;q=0.5, gzip;q=1');

        $expected = array(
            array(
                'encoding' => 'gzip',
                'q' => 1
            ),
            array(
                'encoding' => 'deflate',
                'q' => 0.5
            )
        );

        $this->assertEquals($expected, $encodingList->getEncodings());
    }

    public function testBestMatch()
    {
        $encodingList = new EncodingList('gzip;q=1, deflate;q=0.5');

        $bestMatch = $encodingList->getBestMatch(
            array(
                'gzip',
                'deflate'
            )
        );

        $this->assertEquals('gzip', $bestMatch);
    }

    public function testBestMatchNoMatch()
    {
        $encodingList = new EncodingList('gzip');

        $bestMatch = $encodingList->getBestMatch(
            array(
                'deflate'
            )
        );

        $this->assertFalse($bestMatch);
    }
}
