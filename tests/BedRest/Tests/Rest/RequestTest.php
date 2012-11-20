<?php

namespace BedRest\Tests\Rest;

use BedRest\Tests\BaseTestCase;
use BedRest\Rest\Request;

/**
 * RequestTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RequestTest extends BaseTestCase
{
    /**
     * Request object in test.
     * @var \BedRest\Rest\Request
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

        $this->request = new Request(self::getConfiguration());
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
                'media_range' => 'gzip',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAcceptEncoding()->getMediaTypes());
    }

    public function testSetAcceptEncoding()
    {
        $this->request->setAcceptEncoding('deflate');

        $expected = array(
            array(
                'media_range' => 'deflate',
                'q' => 1
            )
        );

        $this->assertEquals($expected, $this->request->getAcceptEncoding()->getMediaTypes());
    }

    public function testRawBody()
    {
        $rawBody = '{fieldOne:"valueOne",fieldTwo:"valueTwo}';

        $this->request->setRawBody($rawBody);

        $this->assertEquals($rawBody, $this->request->getBody(false));
    }

    public function testBodyDecoded()
    {
        $rawBody = '{"fieldOne":"valueOne","fieldTwo":"valueTwo"}';

        $decodedBody = array(
            'fieldOne' => 'valueOne',
            'fieldTwo' => 'valueTwo'
        );

        $this->request->setRawBody($rawBody);

        $this->assertEquals($decodedBody, $this->request->getBody());
    }

    public function testResource()
    {
        $this->request->setResource('test-resource');

        $this->assertEquals('test-resource', $this->request->getResource());
    }

    public function testRouteComponents()
    {
        $components = array(
            'one' => 'valueOne',
            'two' => 'valueTwo'
        );

        $this->request->setRouteComponents($components);

        $this->assertEquals($components, $this->request->getRouteComponents());
        $this->assertEquals('valueOne', $this->request->getRouteComponent('one'));
        $this->assertEquals('valueTwo', $this->request->getRouteComponent('two'));
    }
}
