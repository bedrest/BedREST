<?php

namespace BedRest\Rest\Event;

use BedRest\Events\Event as BaseEvent;
use BedRest\Rest\Request\Request;

/**
 * Event
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class Event extends BaseEvent
{
    /**
     * @var \BedRest\Rest\Request\Request
     */
    protected $request;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @return \BedRest\Rest\Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \BedRest\Rest\Request\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
