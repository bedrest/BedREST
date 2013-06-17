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
