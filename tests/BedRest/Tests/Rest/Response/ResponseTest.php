<?php

namespace BedRest\Tests\Rest\Response;

use BedRest\Rest\Response\Response;
use BedRest\Tests\BaseTestCase;

/**
 * ResponseTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ResponseTest extends BaseTestCase
{
    /**
     * Response object in test.
     * @var \BedRest\Rest\Response\Response
     */
    protected $response;

    protected function setUp()
    {
        $this->response = new Response();
    }

    public function testResponseCode()
    {
        // test default code is 200
        $this->assertEquals(200, $this->response->getStatusCode());

        // test setting a code
        $this->response->setStatusCode(404);
        $this->assertEquals(404, $this->response->getStatusCode());

        // test setting an invalid code
        $this->setExpectedException('BedRest\Rest\Exception');
        $this->response->setStatusCode(605);
    }

    public function testHeaders()
    {
        $this->assertEmpty($this->response->getHeaders());

        $headers = array(
            'Content-Type' => 'application/json',
            'Content-Encoding' => 'gzip'
        );
        $this->response->setHeaders($headers);
        $this->assertEquals($headers, $this->response->getHeaders());
        $this->assertEquals('application/json', $this->response->getHeader('Content-Type'));

        // ensure headers are case sensitive
        $this->assertNull($this->response->getHeader('content-type'));

        // merging headers
        $newHeaders = array(
            'Content-Language' => 'en-GB',
            'ETag' => md5(time())
        );
        $this->response->setHeaders($newHeaders, true);

        $this->assertEquals($newHeaders['Content-Language'], $this->response->getHeader('Content-Language'));
        $this->assertEquals($newHeaders['ETag'], $this->response->getHeader('ETag'));
        $this->assertEquals($headers['Content-Encoding'], $this->response->getHeader('Content-Encoding'));

        $this->assertEquals(array_merge($headers, $newHeaders), $this->response->getHeaders());

        // overwriting an existing header
        $this->response->setHeader('Content-Type', 'text/xml');
        $this->assertEquals('text/xml', $this->response->getHeader('Content-Type'));

        // setting a new header
        $this->response->setHeader('Age', 30);
        $this->assertEquals(30, $this->response->getHeader('Age'));
    }

    public function testContentType()
    {
        $this->assertEmpty($this->response->getContentType());

        $this->response->setContentType('application/json');
        $this->assertEquals('application/json', $this->response->getContentType());
        $this->assertEquals('application/json', $this->response->getHeader('Content-Type'));

        $this->response->setHeader('Content-Type', 'text/xml');
        $this->assertEquals('text/xml', $this->response->getContentType());
        $this->assertEquals('text/xml', $this->response->getHeader('Content-Type'));
    }

    public function testContent()
    {
        $this->assertEmpty($this->response->getContent());

        $body = json_encode(
            array(
                'item1' => 'value1',
                'item2' => 'value2',
                'item3' => 'value3'
            )
        );
        $this->response->setContent($body);
        $this->assertEquals($body, $this->response->getContent());
    }
}
