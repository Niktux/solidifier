<?php

namespace Solidifier\Dispatcher;

use Solidifier\Dispatcher;
use Solidifier\Event;

class TestDispatcher implements Dispatcher
{
    private
        $events;
    
    public function __construct()
    {
        $this->events = array();
    }
    
    public function dispatch(Event $event)
    {
        $this->events[] = $event;
    }
    
    public function getEvents()
    {
        return $this->events;
    }
}