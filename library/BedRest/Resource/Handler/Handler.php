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

namespace BedRest\Resource\Handler;

use BedRest\Rest\Request;
use BedRest\Rest\Response;

/**
 * Handler
 *
 * This interface is the minimum contract expected of a resource handler.
 *
 * Each method is expected to accept both a Request and Response object, allowing
 * the handler to determine the appropriate action to take depending on any aspect
 * of the Request.
 *
 * The Response object should be populated with the generated output, ensuring that
 * the instance supplied when the method is called is maintained. Failure to do
 * this could result in undefined behaviour.
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
interface Handler
{
    /**
     * Handles a GET request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetResource(Request $request, Response $response);

    /**
     * Handles a GET request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetCollection(Request $request, Response $response);

    /**
     * Handles a POST request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePostResource(Request $request, Response $response);

    /**
     * Handles a POST request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePostCollection(Request $request, Response $response);

    /**
     * Handles a PUT request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePutResource(Request $request, Response $response);

    /**
     * Handles a PUT request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handlePutCollection(Request $request, Response $response);

    /**
     * Handles a DELETE request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleDeleteResource(Request $request, Response $response);

    /**
     * Handles a DELETE request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleDeleteCollection(Request $request, Response $response);
}
