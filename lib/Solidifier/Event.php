<?php

namespace Solidifier;

abstract class Event extends \Symfony\Component\EventDispatcher\Event
{
    const
        EVENT_NAME = 'generic.event';
    
    public function getEventName()
    {
        return static::EVENT_NAME;
    }    
}