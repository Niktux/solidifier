<?php

namespace Solidifier\Events;

use Solidifier\Event;

class ChangeFile extends Event
{
    const
        EVENT_NAME = 'traverse.changeFile';
    
    private
        $currentFile;
    
    public function __construct($currentFile)
    {
        $this->currentFile = $currentFile;
    }
    
    public function getCurrentFile()
    {
        return $this->currentFile;
    }
}