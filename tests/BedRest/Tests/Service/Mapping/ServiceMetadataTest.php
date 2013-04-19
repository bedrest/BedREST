<?php

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
