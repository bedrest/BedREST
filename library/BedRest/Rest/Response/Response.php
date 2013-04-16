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

namespace BedRest\Rest\Response;

use BedRest\Content\Converter\Registry as ContentConverterRegistry;

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
