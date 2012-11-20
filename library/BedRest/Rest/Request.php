<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace BedRest\Rest;

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
     * Configuration object.
     * @var \BedRest\Rest\Configuration
     */
    protected $configuration;

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
     * Components of the route, indexed by name.
     * @var array
     */
    protected $routeComponents = array();

    /**
     * Query string parameters.
     * @var array
     */
    protected $parameters = array();

    /**
     * Content type of the request body.
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
     * Raw body of the request.
     * @var mixed
     */
    protected $body = null;

    /**
     * Constructor.
     * Initialises the Response object with the specified REST configuration.
     * @param \BedRest\Rest\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

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
            $method = $_SERVER['REQUEST_METHOD'];
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
     * Returns a route component.
     * @param  string $name
     * @return array
     */
    public function getRouteComponent($name)
    {
        if (!isset($this->routeComponents[$name])) {
            return null;
        }

        return $this->routeComponents[$name];
    }

    /**
     * Returns route components indexed by name.
     * @return array
     */
    public function getRouteComponents()
    {
        return $this->routeComponents;
    }

    /**
     * Replaces the set of route components with the one provided.
     * @param array $components
     */
    public function setRouteComponents(array $components)
    {
        $this->routeComponents = $components;
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
     * Returns the content type of the request body, usually determined from the 'Content-Type' HTTP header.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets the content type of the request body. If the provided value is null, it is automatically detected
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
     * Set the request body.
     * @param mixed $body
     */
    public function setRawBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the request body, decoded according to the Content-Type specified in the request.
     * @param  boolean           $decode
     * @throws \RuntimeException
     * @return mixed
     */
    public function getBody($decode = true)
    {
        if (!$decode) {
            return $this->body;
        }

        $converterClass = $this->configuration->getContentConverter($this->getContentType());

        // TODO: check if content type is supported
        if (empty($converterClass)) {
            throw new \RuntimeException('Invalid content type specified in Accept.');
        } elseif (!class_exists($converterClass)) {
            throw new \RuntimeException('Content converter class could not be found.');
        }

        $converter = new $converterClass;

        $data = $converter->decode($this->body);

        return $data;
    }
}
