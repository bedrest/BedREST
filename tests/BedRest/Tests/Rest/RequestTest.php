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
        'HTTP_CONTENT_TYPE' => 'application/json',
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

    public function testAcceptBestMatch()
    {
        $this->request->setAccept('application/json;q=1, text/xml;q=0.5');

        $bestMatch = $this->request->getAcceptBestMatch(
            array(
                'application/json',
                'text/xml'
            )
        );

        $this->assertEquals('application/json', $bestMatch);
    }

    public function testAcceptBestMatchNoMatch()
    {
        $this->request->setAccept('application/json');

        $bestMatch = $this->request->getAcceptBestMatch(
            array(
                'text/xml'
            )
        );

        $this->assertFalse($bestMatch);
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

    public function testRawPayload()
    {
        $rawPayload = '{fieldOne:"valueOne",fieldTwo:"valueTwo}';

        $this->request->setRawPayload($rawPayload);

        $this->assertEquals($rawPayload, $this->request->getPayload(false));
    }

    public function testPayloadDecoded()
    {
        $rawPayload = '{"fieldOne":"valueOne","fieldTwo":"valueTwo"}';

        $decodedPayload = new \stdClass();
        $decodedPayload->fieldOne = 'valueOne';
        $decodedPayload->fieldTwo = 'valueTwo';

        $this->request->setRawPayload($rawPayload);

        $this->assertEquals($decodedPayload, $this->request->getPayload());
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
