<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\TestFixtures\Services\Company;

use BedRest\Rest\Request\Request;
use BedRest\Service\Mapping\Annotation as BedRest;

/**
 * Generic
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class Generic
{
    /**
     * Returns values for metadata to be compared against in tests.
     * @return array
     */
    public function getGenericMetadata()
    {
        return array(
            'className' => __CLASS__,
            'listeners' => array(
                'GET' => array(
                    'get'
                ),
                'GET_COLLECTION' => array(
                    'getCollection'
                ),
                'POST' => array(
                    'post'
                ),
                'POST_COLLECTION' => array(
                    'postCollection'
                ),
                'PUT' => array(
                    'put'
                ),
                'PUT_COLLECTION' => array(
                    'putCollection'
                ),
                'DELETE' => array(
                    'delete'
                ),
                'DELETE_COLLECTION' => array(
                    'deleteCollection'
                ),
            )
        );
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET")
     */
    public function get(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="GET_COLLECTION")
     */
    public function getCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST")
     */
    public function post(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="POST_COLLECTION")
     */
    public function postCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT")
     */
    public function put(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="PUT_COLLECTION")
     */
    public function putCollection(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE")
     */
    public function delete(Request $request)
    {
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     *
     * @BedRest\Listener(event="DELETE_COLLECTION")
     */
    public function deleteCollection(Request $request)
    {
    }
}
