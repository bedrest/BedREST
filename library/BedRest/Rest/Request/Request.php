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

namespace BedRest\Rest\Request;

use BedRest\Content\Negotiation\EncodingList;
use BedRest\Content\Negotiation\MediaTypeList;

/**
 * Request
 *
 * Contains data about an HTTP request, along with utility
 * functions for checking against various parameters supplied in the request.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Request
{
    /**
     * HTTP method of the request.
     * @var string
     */
    protected $method;

    /**
     * Name of the resource being requested.
     * @var string
     */
    protected $resource = '';

    /**
     * Request parameters, sourced from routing and the query string.
     * @var array
     */
    protected $parameters = array();

    /**
     * Request Content-Type.
     * @var string
     */
    protected $contentType = '';

    /**
     * Parsed and ordered 'Accept' header.
     * @var \BedRest\Content\Negotiation\MediaTypeList
     */
    protected $accept = array();

    /**
     * Parsed and ordered 'Accept-Encoding' header.
     * @var \BedRest\Content\Negotiation\MediaTypeList
     */
    protected $acceptEncoding = array();

    /**
     * Request content.
     * @var mixed
     */
    protected $content = null;

    /**
     * Constructor.
     * By default, the Request object will be populated from environment settings (such as $_SERVER and $_GET).
     */
    public function __construct()
    {
        $this->setMethod();

        $this->setContentType();

        $this->setAccept();

        $this->setAcceptEncoding();

        $this->setParameters($_GET);
    }

    /**
     * Returns the HTTP method of the request.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the HTTP method of the request. If the provided value is null, it is automatically detected from the
     * environment.
     * @param string $method
     */
    public function setMethod($method = null)
    {
        if ($method === null) {
            $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
        }

        $this->method = $method;
    }

    /**
     * Returns the resource referenced by the request.
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Sets the resource referenced by the request.
     * @param string $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Returns the query string parameters.
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the value of a single query string parameter.
     * @param  string $parameter
     * @param  mixed  $default
     * @return mixed
     */
    public function getParameter($parameter, $default = null)
    {
        if (!isset($this->parameters[$parameter])) {
            return $default;
        }

        return $this->parameters[$parameter];
    }

    /**
     * Sets the query string parameters, discarding all existing values.
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets an individual query string parameter.
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }

    /**
     * Returns the content of the Content-Type header, if supplied.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets the content type of the request content. If the provided value is null, it is automatically detected
     * from the environment.
     * @param string $contentType
     */
    public function setContentType($contentType = null)
    {
        if ($contentType === null) {
            $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;
        }

        $this->contentType = $contentType;
    }

    /**
     * Returns the parsed accepted content types of the request, usually determined by the 'Accept' HTTP header.
     * @return \BedRest\Content\Negotiation\MediaTypeList
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Sets the accepted content types of the request. If the provided value is null, it is automatically detected
     * from the environment. The provided value is parsed into an array and ordered by weighting of each format.
     * @param array $accept
     */
    public function setAccept($accept = null)
    {
        if ($accept === null) {
            $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
        }

        $this->accept = new MediaTypeList($accept);
    }

    /**
     * Returns the parsed accepted content types of the request, usually determined by the 'Accept-Encoding'
     * HTTP header.
     * @return \BedRest\Content\Negotiation\EncodingList
     */
    public function getAcceptEncoding()
    {
        return $this->acceptEncoding;
    }

    /**
     * Sets the accepted content types of the request. If the provided value is null, it is automatically detected
     * from the environment. The provided value is parsed into an array and ordered by weighting of each format.
     * @param array $acceptEncoding
     */
    public function setAcceptEncoding($acceptEncoding = null)
    {
        if ($acceptEncoding === null) {
            $acceptEncoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : null;
        }

        $this->acceptEncoding = new EncodingList($acceptEncoding);
    }

    /**
     * Set the request content.
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the request content.
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
