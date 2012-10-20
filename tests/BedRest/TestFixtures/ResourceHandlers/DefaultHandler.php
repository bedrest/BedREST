<?php

namespace BedRest\TestFixtures\ResourceHandlers;

use BedRest\Resource\Handler\Handler;
use BedRest\Rest\Request;
use BedRest\Rest\Response;

/**
 * DefaultHandler
 *
 * Author: Geoff Adams <geoff@dianode.net>
 */
class DefaultHandler implements Handler
{
    /**
     * Handles a GET request for a single resource.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetResource(Request $request, Response $response)
    {
    }

    /**
     * Handles a GET request for a collection of resources.
     * @param \BedRest\Rest\Request  $request
     * @param \BedRest\Rest\Response $response
     */
    public function handleGetCollection(Request $request, Response $response)
    {
    }
}
