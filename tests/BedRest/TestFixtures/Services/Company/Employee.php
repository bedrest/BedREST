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

use BedRest\Service\Mapping\Annotation as BedRest;

/**
 * Employee
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service
 */
class Employee extends Generic
{
    /**
     * Returns values for metadata to be compared against in tests.
     * @return array
     */
    public function getMetadata()
    {
        return array(
            'className' => __CLASS__,
            'listeners' => array(
                'eventOne' => array(
                    'listenerOne'
                ),
                'eventTwo' => array(
                    'listenerOne',
                    'listenerTwo'
                )
            )
        );
    }

    /**
     * Event listener.
     * @BedRest\Listener(event="eventOne")
     * @BedRest\Listener(event="eventTwo")
     */
    public function listenerOne()
    {
    }

    /**
     * Event listener.
     * @BedRest\Listener(event="eventTwo")
     */
    public function listenerTwo()
    {
    }

    /**
     * Not an event listener.
     */
    public function notAListener()
    {
    }
}
