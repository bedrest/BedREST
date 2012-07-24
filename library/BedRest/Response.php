<?php

namespace BedRest;

/**
 * Response
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Response
{
    /**
     * Raw body content.
     * @var string
     */
    protected $rawBody = '';
    
    /**
     * Content type of the response.
     * @var string
     */
    protected $contentType = '';
    
    /**
     * HTTP headers.
     * @var array
     */
    protected $headers = array();
    
    /**
     * HTTP response code.
     * @var integer
     */
    protected $code = 200;
    
    /**
     * Sets the raw body content.
     * @param string $rawBody
     */
    public function setRawBody($rawBody)
    {
        $this->rawBody = $rawBody;
    }
    
    /**
     * Returns the raw body content.
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }
    
    /**
     * Sets the Content-Type of the response.
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
    
    /**
     * Returns the Content-Type of the response.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }
    
    /**
     * Sets all HTTP headers, replacing all existing headers unless the $merge parameter is set to true.
     * @param array $headers
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
     * @param string $name
     * @return string
     * @throws BedRest\Exception
     */
    public function getHeader($name)
    {
        if (!isset($this->headers[$name])) {
            throw new \BedRest\Exception("HTTP header with name '$name' does not exist.");
        }
        
        return $this->headers[$name];
    }
    
    /**
     * Sets the HTTP response code.
     * @param integer $code
     */
    public function setResponseCode($code)
    {
        if (!is_int($code) || ($code < 100) || ($code > 599)) {
            throw new \BedRest\Exception("Invalid HTTP response code.");
        }
        
        $this->code = $code;
    }
    
    /**
     * Returns the HTTP response code.
     * @return integer
     */
    public function getResponseCode()
    {
        return $this->code;
    }
}
