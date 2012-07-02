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

namespace BedRest;

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
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    
    protected $method;
    
    protected $resource = '';
    
    protected $contentType = '';
    
    protected $accept = array();
    
    protected $acceptEncoding = array();
    
    public function __construct()
    {
        $this->setMethod();
        
        $this->setContentType();
        
        $this->setAccept();
        
        $this->setAcceptEncoding();
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function setMethod($method = null)
    {
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }
        
        $this->method = $method;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function setResource($resource = null)
    {
        if ($resource === null) {
            // TODO: detect resource? is this even possible at this stage of the request?
        }
        
        $this->resource = $resource;
    }
    
    public function getContentType()
    {
        return $this->contentType;
    }
    
    public function setContentType($contentType = null)
    {
        if ($contentType === null) {
            $contentType = isset($_SERVER['HTTP_CONTENT_TYPE']) ? $_SERVER['HTTP_CONTENT_TYPE'] : null;
        }
        
        $this->contentType = $contentType;
    }
    
    public function getAccept()
    {
        return $this->accept;
    }
    
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
                if (count($param) != 2) continue;
                
                $entry[$param[0]] = $param[1];
            }
            
            $this->accept[] = $entry;
        }
        
        // @todo Take account of specificity with wildcard media ranges (see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html)
        $this->mergeSort($this->accept, array($this, 'sortQualityComparator'));
    }
    
    public function getAcceptEncoding()
    {
        return $this->acceptEncoding;
    }
    
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
                if (count($param) != 2 || $param[0] != 'q') continue;
                
                $entry['q'] = $param[1];
            }
            
            $this->acceptEncoding[] = $entry;
        }
        
        // sort according to quality factor
        $this->mergeSort($this->acceptEncoding, array($this, 'sortQualityComparator'));
    }
    
    /**
     * Comparator function for sorting a list of arrays by their 'q' factor.
     * @param array $e1
     * @param array $e2
     * @return integer 
     */
    protected function sortQualityComparator($e1, $e2) {
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
     * Sorts the parsed accept header using a merge sort algorithm, in order to maintain order as presented in the header.
     * All of this code has been taken from http://www.php.net/manual/en/function.usort.php#38827, some changes made
     * are purely for readability and code style.
     * @param array $array
     */
    protected function mergeSort(array &$array, $comparator = 'strcmp') {
        // optimisation, no sorting required on arrays with 0 or 1 items
        if (count($array) < 2) return;
        
        // split the array in half
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway);
        $array2 = array_slice($array, $halfway);
        
        // use recursion to sort both halves
        $this->mergeSort($array1);
        $this->mergeSort($array2);
        
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
        while ($ptr1 < count($array1)) $array[] = $array1[$ptr1++];
        while ($ptr2 < count($array2)) $array[] = $array2[$ptr2++];
    }
}
