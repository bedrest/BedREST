<?php

namespace BedRest;

use BedRest\Event\Event;

/**
 * EventManager
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class EventManager
{
    /**
     * List of event listeners.
     * @var array
     */
    protected $listeners = array();
    
    /**
     * Adds a listener.
     * @param string $event
     * @param callable $observer
     */
    public function addListener($event, $observer)
    {
        if (!is_array($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }
        
        $this->listeners[$event][] = $observer;
    }
    
    /**
     * Adds a set of listeners.
     * @param string $event
     * @param callable $observers
     */
    public function addListeners($event, $observers)
    {
        foreach ($observers as $observer) {
            $this->addListener($event, $observer);
        }
    }
    
    /**
     * Retrieves all listeners for an event.
     * @param string $event
     * @return array
     */
    public function getListeners($event)
    {
        if (!is_array($this->listeners[$event])) {
            return array();
        }
        
        return $this->listeners[$event];
    }
    
    /**
     * Dispatches an event to all listeners.
     * @param string $event
     * @param \BedRest\Event\Event $eventObject
     */
    public function dispatch($event, Event $eventObject)
    {
        if (!is_array($this->listeners[$event])) {
            return;
        }
        
        foreach ($this->listeners[$event] as $observer) {
            call_user_func_array($observer, array($eventObject));
        }
    }
}
