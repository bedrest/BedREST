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

namespace BedRest\Service\Mapping;

/**
 * ServiceMetadata
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class ServiceMetadata
{
    /**
     * Class name of the service.
     * @var string
     */
    protected $className;

    /**
     * Event listeners for the service.
     * @var string
     */
    protected $listeners = array();

    /**
     * Constructor.
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Sets the service class name.
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the service class name.
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Adds a listener for the specified event.
     * @param string $event
     * @param string $method
     */
    public function addListener($event, $method)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }

        $this->listeners[$event][] = $method;
    }

    /**
     * Returns the set of listeners for a specified event.
     * @param  string $event
     * @return array
     */
    public function getListeners($event)
    {
        if (!isset($this->listeners[$event])) {
            return array();
        }

        return $this->listeners[$event];
    }

    /**
     * Sets all listeners, discarding the current set.
     * @param array $listeners
     */
    public function setAllListeners($listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * Returns the set of all listeners, indexed by event.
     * @return array
     */
    public function getAllListeners()
    {
        return $this->listeners;
    }
}
