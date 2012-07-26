<?php

namespace BedRest\Tests;

use BedRest\Request;

/**
 * RequestTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RequestTest extends BaseTestCase
{
    /**
     * Request object in test.
     * @var BedRest\Request
     */
    protected $request;

    public function setUp()
    {
        $_SERVER = array(
            'REQUEST_METHOD' => 'POST',
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_ACCEPT_ENCODING' => 'gzip'
        );

        $this->request = new Request();
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
        $this->assertEquals($_SERVER['HTTP_CONTENT_TYPE'], $this->request->getContentType());
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

        $this->assertEquals($expected, $this->request->getAccept());
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

        $this->assertEquals($expected, $this->request->getAccept());
    }

    public function testMultipleAccept()
    {
        $this->request->setAccept('application/json, text/xml');

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

        $this->assertEquals($expected, $this->request->getAccept());
    }

    public function testMultipleAcceptQualityOrdering()
    {
        $this->request->setAccept('text/xml;q=0.5, application/json;q=1');

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

        $this->assertEquals($expected, $this->request->getAccept());
    }

    public function testAcceptEncodingDetected()
    {
        $expected = array(
            array(
                'encoding' => 'gzip',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAcceptEncoding());
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

        $this->assertEquals($expected, $this->request->getAcceptEncoding());
    }

    public function testMultipleAcceptEncoding()
    {
        $this->request->setAcceptEncoding('gzip, deflate');

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

        $this->assertEquals($expected, $this->request->getAcceptEncoding());
    }

    public function testMultipleAcceptEncodingQualityOrdering()
    {
        $this->request->setAcceptEncoding('deflate;q=0.5, gzip;q=1');

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

        $this->assertEquals($expected, $this->request->getAcceptEncoding());
    }
}

