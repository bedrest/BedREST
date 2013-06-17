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

namespace BedRest\Tests\Content\Converter;

use BedRest\Content\Converter\Json;
use BedRest\Tests\BaseTestCase;

/**
 * JsonTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class JsonTest extends BaseTestCase
{
    protected $decodedData = array(
        'one' => '1',
        'two' => '2',
        'three' => '3'
    );

    protected $encodedString = '{"one":"1","two":"2","three":"3"}';

    public function testInvalidJsonThrowsException()
    {
        $this->setExpectedException('BedRest\Content\Converter\Exception');

        $converter = new Json();

        $converter->decode('{not valid JSON');
    }

    public function testBasicEncode()
    {
        $converter = new Json();

        $json = $converter->encode($this->decodedData);

        $this->assertEquals($this->encodedString, $json);
    }

    public function testBasicDecode()
    {
        $converter = new Json();

        $object = $converter->decode($this->encodedString);

        $this->assertEquals($this->decodedData, $object);
    }
}
