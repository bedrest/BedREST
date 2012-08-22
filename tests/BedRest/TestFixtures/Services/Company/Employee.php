<?php

namespace BedRest\TestFixtures\Services\Company;

use BedRest\Service\Mapping\Annotation as BedRest;

/**
 * Employee
 *
 * @author Geoff Adams <geoff@dianode.net>
 * 
 * @BedRest\Service
 */
class Employee
{
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
    public function get()
    {
    }
}
