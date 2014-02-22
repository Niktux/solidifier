<?php

namespace Solidifier\Dispatchers;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Solidifier\Dispatcher;
use Solidifier\Event;

class EventDispatcher implements Dispatcher
{
    private
        $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function dispatch(Event $event)
    {
        $this->dispatcher->dispatch($event->getEventName(), $event);
    }
}