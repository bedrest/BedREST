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
