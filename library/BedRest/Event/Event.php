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

namespace BedRest\Event;

use BedRest\Request;
use BedRest\RestManager;

/**
 * RestEvent
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RestEvent
{
    /**
     * RestManager instance
     * @var BedRest\RestManager 
     */
    protected $restManager;
    
    /**
     * Request.
     * @var BedRest\Request
     */
    protected $request;
    
    /**
     * Sets the RestManager instance that dispatched the event.
     * @param BedRest\RestManager $restManager
     */
    public function setRestManager(RestManager $restManager)
    {
        $this->restManager = $restManager;
    }
    
    /**
     * Returns the RestManager instance that dispatched the event.
     * @return \BedRest\RestManager
     */
    public function getRestManager()
    {
        return $this->restManager;
    }
    
    /**
     * Sets the Request instance.
     * @param BedRest\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * Returns the Request instance.
     * @return \BedRest\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}

