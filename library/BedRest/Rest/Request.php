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
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_GET_COLLECTION = 'GET_COLLECTION';
    const METHOD_POST = 'POST';
    const METHOD_POST_COLLECTION = 'POST_COLLECTION';
    const METHOD_PUT = 'PUT';
    const METHOD_PUT_COLLECTION = 'PUT_COLLECTION';
    const METHOD_DELETE = 'DELETE';
    const METHOD_DELETE_COLLECTION = 'DELETE_COLLECTION';

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

    const CONTENTTYPE_JSON = 'application/json';
    const CONTENTTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const CONTENTTYPE_XML = 'text/xml';

    /**
     * Content type of any request payload.
     * @var string
     */
    protected $contentType = '';

    /**
     * Parsed and ordered 'Accept' header.
     * @var array
     */
    protected $accept = array();

    /**
     * Parsed and ordered 'Accept-Encoding' header.
     * @var array
     */
    protected $acceptEncoding = array();

    /**
     * Raw payload of the request.
     * @var mixed
     */
    protected $payload = null;

    /**
     * Constructor.
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
     * Returns the content type of the request payload, usually determined from the 'Content-Type' HTTP header.
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets the content type of the request payload. If the provided value is null, it is automatically detected
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
     * @return array
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

        // split accept into components
        $accept = explode(',', $accept);

        $this->accept = array();

        foreach ($accept as $item) {
            $item = explode(';', trim($item));

            $entry = array(
                'media_range' => array_shift($item),
                'q' => 1
            );

            foreach ($item as $param) {
                $param = explode('=', $param);

                // ignore malformed params
                if (count($param) != 2) {
                    continue;
                }

                $entry[$param[0]] = $param[1];
            }

            $this->accept[] = $entry;
        }

        // @todo Take account of specificity with wildcard media ranges
        // (see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html)
        $this->mergeSort($this->accept, array($this, 'sortQualityComparator'));
    }

    /**
     * Gets the best match format to return a response in based on a supplied list of formats.
     * @param  array          $formats
     * @return string|boolean
     */
    public function getAcceptBestMatch(array $formats)
    {
        foreach ($this->accept as $accept) {
            if (in_array($accept['media_range'], $formats)) {
                return $accept['media_range'];
            }
        }

        return false;
    }

    /**
     * Returns the parsed accepted content types of the request, usually determined by the 'Accept-Encoding'
     * HTTP header.
     * @return array
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

        // split accept into components
        $acceptEncoding = explode(',', $acceptEncoding);

        $this->acceptEncoding = array();

        foreach ($acceptEncoding as $item) {
            $item = explode(';', trim($item));

            $entry = array(
                'encoding' => array_shift($item),
                'q' => 1
            );

            foreach ($item as $param) {
                $param = explode('=', $param);

                // ignore malformed params and anything which isn't the quality factor
                if (count($param) != 2 || $param[0] != 'q') {
                    continue;
                }

                $entry['q'] = $param[1];
            }

            $this->acceptEncoding[] = $entry;
        }

        // sort according to quality factor
        $this->mergeSort($this->acceptEncoding, array($this, 'sortQualityComparator'));
    }

    /**
     * Set the request payload.
     * @param mixed $payload
     */
    public function setRawPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Returns the request payload, decoded according to the Content-Type specified in the request.
     * @TODO allow registration of an arbitrary set of content converters to be used in here.
     * @param  boolean           $decode
     * @throws \RuntimeException
     * @return mixed
     */
    public function getPayload($decode = true)
    {
        if (!$decode) {
            return $this->payload;
        }

        switch ($this->contentType) {
            case self::CONTENTTYPE_JSON:
                $data = json_decode($this->payload);
                break;
            case self::CONTENTTYPE_URLENCODED:
                $data = urldecode($this->payload);
                break;
            case self::CONTENTTYPE_XML:
                $data = simplexml_load_string($this->payload);
                break;
            default:
                throw new \RuntimeException("The Content-Type '{$this->contentType}' is unsupported.");
                break;
        }

        return $data;
    }

    /**
     * Comparator function for sorting a list of arrays by their 'q' factor.
     * @param  array   $e1
     * @param  array   $e2
     * @return integer
     */
    protected function sortQualityComparator($e1, $e2)
    {
        $r = $e2['q'] - $e1['q'];

        // doesn't cope too well with values 1 > v > -1, so make sure we return simple integers
        if ($r > 0) {
            $r = 1;
        } elseif ($r < 0) {
            $r = -1;
        }

        return $r;
    }

    /**
     * Sorts the parsed accept header using a merge sort algorithm, in order to maintain order as presented in the
     * header.
     *
     * All of this code has been taken from http://www.php.net/manual/en/function.usort.php#38827, some changes made
     * are purely for readability and code style.
     * @param  array  $array
     * @param  string $comparator
     * @return null
     */
    protected function mergeSort(array &$array, $comparator = 'strcmp')
    {
        // optimisation, no sorting required on arrays with 0 or 1 items
        if (count($array) < 2) {
            return;
        }

        // split the array in half
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway);
        $array2 = array_slice($array, $halfway);

        // use recursion to sort both halves
        $this->mergeSort($array1, $comparator);
        $this->mergeSort($array2, $comparator);

        // optimisation, if the end of $array1 is less than the start of $array2, append and return
        if (call_user_func($comparator, end($array1), $array2[0]) < 1) {
            $array = array_merge($array1, $array2);

            return;
        }

        // merge the arrays into a single array
        $array = array();
        $ptr1 = $ptr2 = 0;

        while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
            if (call_user_func($comparator, $array1[$ptr1], $array2[$ptr2]) < 1) {
                $array[] = $array1[$ptr1++];
            } else {
                $array[] = $array2[$ptr2++];
            }
        }

        // merge the remainder
        while ($ptr1 < count($array1)) {
            $array[] = $array1[$ptr1++];
        }

        while ($ptr2 < count($array2)) {
            $array[] = $array2[$ptr2++];
        }
    }
}
