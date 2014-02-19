<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefectDispatcher
{
    private
        $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function dispatch(Defect $event)
    {
        $this->dispatcher->dispatch($event->getEventName(), $event);
    }
}