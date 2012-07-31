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
     * Configuration object.
     * @var \BedRest\Configuration
     */
    protected $configuration;
    
    /**
     * Body as set by the service layer.
     * @var mixed
     */
    protected $body;
    
    /**
     * Whether the body has been processed into the raw body string.
     * @var boolean
     */
    protected $bodyProcessed = false;
    
    /**
     * Raw body content.
     * @var string
     */
    protected $rawBody;

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
    
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
    
    /**
     * Sets the body content.
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        $this->bodyProcessed = false;
        $this->rawBody = null;
    }
    
    /**
     * Returns the body content.
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the raw body content.
     * @return string
     */
    public function getRawBody()
    {
        if (!$this->bodyProcessed) {
            switch ($this->getContentType()) {
                case 'application/json':
                    $mapper = new DataMapper\JsonMapper($this->configuration);
                    $this->rawBody = $mapper->reverseGeneric($this->body);
                    break;
                default:
                    throw RestException::notAcceptable();
                    break;
            }
            
            $this->bodyProcessed = true;
        }
        
        return $this->rawBody;
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

