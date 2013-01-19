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
    public static $handleGetResourceCalled = 0;

    public static $handleGetCollectionCalled = 0;

    public static $handlePostResourceCalled = 0;

    public static $handlePostCollectionCalled = 0;

    public static $handlePutResourceCalled = 0;

    public static $handlePutCollectionCalled = 0;

    public static $handleDeleteResourceCalled = 0;

    public static $handleDeleteCollectionCalled = 0;

    public function handleGetResource(Request $request, Response $response)
    {
        self::$handleGetResourceCalled++;
    }

    public function handleGetCollection(Request $request, Response $response)
    {
        self::$handleGetCollectionCalled++;
    }

    public function handlePostResource(Request $request, Response $response)
    {
        self::$handlePostResourceCalled++;
    }

    public function handlePostCollection(Request $request, Response $response)
    {
        self::$handlePostCollectionCalled++;
    }

    public function handlePutResource(Request $request, Response $response)
    {
        self::$handlePutResourceCalled++;
    }

    public function handlePutCollection(Request $request, Response $response)
    {
        self::$handlePutCollectionCalled++;
    }

    public function handleDeleteResource(Request $request, Response $response)
    {
        self::$handleDeleteResourceCalled++;
    }

    public function handleDeleteCollection(Request $request, Response $response)
    {
        self::$handleDeleteCollectionCalled++;
    }
}
