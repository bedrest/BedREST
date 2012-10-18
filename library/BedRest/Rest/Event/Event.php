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

namespace BedRest\Rest\Event;

use BedRest\Rest\Request;
use BedRest\Rest\Response;
use BedRest\Rest\RestManager;
use BedRest\Events\Event as EventBase;

/**
 * Event
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Event extends EventBase
{
    /**
     * RestManager instance
     * @var \BedRest\Rest\RestManager
     */
    protected $restManager;

    /**
     * Request.
     * @var \BedRest\Rest\Request
     */
    protected $request;

    /**
     * Response.
     * @var \BedRest\Rest\Response
     */
    protected $response;

    /**
     * Sets the RestManager instance that dispatched the event.
     * @param \BedRest\Rest\RestManager $restManager
     */
    public function setRestManager(RestManager $restManager)
    {
        $this->restManager = $restManager;
    }

    /**
     * Returns the RestManager instance that dispatched the event.
     * @return \BedRest\Rest\RestManager
     */
    public function getRestManager()
    {
        return $this->restManager;
    }

    /**
     * Sets the Request instance.
     * @param \BedRest\Rest\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the Request instance.
     * @return \BedRest\Rest\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the Response instance.
     * @param \BedRest\Rest\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the Response instance.
     * @return \BedRest\Rest\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}