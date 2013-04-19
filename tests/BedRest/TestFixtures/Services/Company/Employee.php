<?php

namespace BedRest\TestFixtures\Services\Company;

use BedRest\Service\Mapping\Annotation as BedRest;

/**
 * Employee
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Service(type="BASIC", dataMapper="BedRest\TestFixtures\Services\Company\DataMapper\Generic")
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
            'type' => \BedRest\Service\Mapping\ServiceMetadata::TYPE_BASIC,
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
