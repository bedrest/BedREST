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

namespace BedRest\Rest\Response;

/**
 * Response
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Response
{
    /**
     * Response content.
     * @var mixed
     */
    protected $content;

    /**
     * HTTP headers.
     * @var array
     */
    protected $headers = array();

    /**
     * HTTP status code.
     * @var integer
     */
    protected $statusCode = 200;

    /**
     * Sets the response content.
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the response content.
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the Content-Type of the response.
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->headers['Content-Type'] = $contentType;
    }

    /**
     * Returns the Content-Type of the response.
     * @return string
     */
    public function getContentType()
    {
        if (!isset($this->headers['Content-Type'])) {
            return null;
        }

        return $this->headers['Content-Type'];
    }

    /**
     * Sets all HTTP headers, replacing all existing headers unless the $merge parameter is set to true.
     * @param array   $headers
     * @param boolean $merge
     */
    public function setHeaders(array $headers, $merge = false)
    {
        if ($merge) {
            $headers = array_merge($this->headers, $headers);
        }

        $this->headers = $headers;
    }

    /**
     * Gets all HTTP headers as an array.
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets a single HTTP header.
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Returns a single HTTP header.
     * @param  string $name
     * @return string
     */
    public function getHeader($name)
    {
        if (!isset($this->headers[$name])) {
            return null;
        }

        return $this->headers[$name];
    }

    /**
     * Sets the HTTP status code.
     * @param  integer                 $code
     * @throws \BedRest\Rest\Exception
     */
    public function setStatusCode($code)
    {
        if (!is_int($code) || ($code < 100) || ($code > 599)) {
            throw new \BedRest\Rest\Exception("Invalid HTTP response code.");
        }

        $this->statusCode = $code;
    }

    /**
     * Returns the HTTP status code.
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
