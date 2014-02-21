<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Dispatcher
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