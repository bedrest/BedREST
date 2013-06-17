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

namespace BedRest\Tests\Rest\Request;

use BedRest\Tests\BaseTestCase;
use BedRest\Rest\Request\Request;

/**
 * RequestTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RequestTest extends BaseTestCase
{
    /**
     * Request object in test.
     * @var \BedRest\Rest\Request\Request
     */
    protected $request;

    /**
     * Stores the default server environment.
     * @var array
     */
    protected $defaultServerEnvironment;

    /**
     * Server environment variables for auto-detection tests.
     * @var array
     */
    protected $serverEnvironment = array(
        'REQUEST_METHOD' => 'POST',
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_ACCEPT_ENCODING' => 'gzip'
    );

    protected function setUp()
    {
        $this->defaultServerEnvironment = $_SERVER;
        $_SERVER = $this->serverEnvironment;

        $this->request = new Request();
    }

    protected function tearDown()
    {
        $_SERVER = $this->defaultServerEnvironment;
        unset($this->defaultServerEnvironment);
    }

    public function testMethodDetected()
    {
        $this->assertEquals($_SERVER['REQUEST_METHOD'], $this->request->getMethod());
    }

    public function testSetMethod()
    {
        $this->request->setMethod('PUT');

        $this->assertEquals('PUT', $this->request->getMethod());
    }

    public function testContentTypeDetected()
    {
        $this->assertEquals($_SERVER['CONTENT_TYPE'], $this->request->getContentType());
    }

    public function testSetContentType()
    {
        $this->request->setContentType('text/xml');

        $this->assertEquals('text/xml', $this->request->getContentType());
    }

    public function testAcceptDetected()
    {
        $expected = array(
            array(
                'media_range' => 'application/json',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAccept()->getMediaTypes());
    }

    public function testSetAccept()
    {
        $this->request->setAccept('text/xml');

        $expected = array(
            array(
                'media_range' => 'text/xml',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAccept()->getMediaTypes());
    }

    public function testAcceptEncodingDetected()
    {
        $expected = array(
            array(
                'encoding' => 'gzip',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAcceptEncoding()->getEncodings());
    }

    public function testSetAcceptEncoding()
    {
        $this->request->setAcceptEncoding('deflate');

        $expected = array(
            array(
                'encoding' => 'deflate',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAcceptEncoding()->getEncodings());
    }

    public function testContent()
    {
        $content = array(
            'fieldOne' => 'valueOne',
            'fieldTwo' => 'valueTwo'
        );

        $this->request->setContent($content);
        $this->assertEquals($content, $this->request->getContent());
    }

    public function testResource()
    {
        $this->request->setResource('test-resource');

        $this->assertEquals('test-resource', $this->request->getResource());
    }

    public function testParameters()
    {
        $components = array(
            'one' => 'valueOne',
            'two' => 'valueTwo'
        );

        $this->request->setParameters($components);

        $this->assertEquals($components, $this->request->getParameters());
        $this->assertEquals('valueOne', $this->request->getParameter('one'));
        $this->assertEquals('valueTwo', $this->request->getParameter('two'));

        $this->request->setParameter('three', 'valueThree');
        $this->assertEquals('valueThree', $this->request->getParameter('three'));

        $this->request->setParameter('two', 'valueTwoModified');
        $this->assertEquals('valueTwoModified', $this->request->getParameter('two'));
    }
}
