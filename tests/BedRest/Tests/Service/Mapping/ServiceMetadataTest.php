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

namespace BedRest\Tests\Service\Mapping;

use BedRest\Service\Mapping\ServiceMetadata;
use BedRest\Tests\BaseTestCase;

/**
 * ServiceMetadataTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadataTest extends BaseTestCase
{
    public function testClassName()
    {
        $meta = new ServiceMetadata('Service\Test');
        $this->assertEquals('Service\Test', $meta->getClassName());

        $meta->setClassName('Service\TestTwo');
        $this->assertEquals('Service\TestTwo', $meta->getClassName());
    }

    public function testListeners()
    {
        $meta = new ServiceMetadata('Service\Test');
        $this->assertEmpty($meta->getAllListeners());
        $this->assertEmpty($meta->getListeners('event'));

        $listeners = array(
            'eventOne' => array(
                'listenerOne'
            ),
            'eventTwo' => array(
                'listenerOne',
                'listenerTwo'
            )
        );

        $meta->setAllListeners($listeners);
        $this->assertEquals($listeners, $meta->getAllListeners());
        $this->assertEquals($listeners['eventOne'], $meta->getListeners('eventOne'));
        $this->assertEquals($listeners['eventTwo'], $meta->getListeners('eventTwo'));

        $eventTwoListeners = $listeners['eventTwo'];
        $eventTwoListeners[] = 'listenerThree';
        $meta->addListener('eventTwo', 'listenerThree');
        $this->assertEquals($eventTwoListeners, $meta->getListeners('eventTwo'));
    }
}
