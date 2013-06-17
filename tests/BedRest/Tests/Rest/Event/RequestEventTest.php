<?php

namespace BedRest\Tests\Rest\Event;

use BedRest\Rest\Event\RequestEvent;
use BedRest\Tests\BaseTestCase;

/**
 * RequestEventTest
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class RequestEventTest extends BaseTestCase
{
    /** @var \BedRest\Rest\Event\RequestEvent */
    protected $event;

    protected function setUp()
    {
        $this->event = new RequestEvent();
    }

    public function testRequest()
    {
        $mockRequest = $this->getMock('BedRest\Rest\Request\Request');

        $this->event->setRequest($mockRequest);
        $this->assertEquals($mockRequest, $this->event->getRequest());
    }

    public function testData()
    {
        $testData = array(
            'test'          => 'value',
            'another_test'  => 'another_value'
        );

        $this->event->setData($testData);
        $this->assertEquals($testData, $this->event->getData());
    }
}
