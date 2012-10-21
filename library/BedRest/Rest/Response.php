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
 * Response
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Response
{
    /**
     * Configuration object.
     * @var \BedRest\Rest\Configuration
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
            $converterClass = $this->configuration->getContentConverter($this->getContentType());
            $converter = new $converterClass;

            $this->rawBody = $converter->encode($this->body);
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
     * @param  string                  $name
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
     * Sets the HTTP response code.
     * @param  integer                 $code
     * @throws \BedRest\Rest\Exception
     */
    public function setResponseCode($code)
    {
        if (!is_int($code) || ($code < 100) || ($code > 599)) {
            throw new \BedRest\Rest\Exception("Invalid HTTP response code.");
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
