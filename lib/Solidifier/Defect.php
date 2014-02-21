<?php

namespace Solidifier;

use PhpParser\Node;

abstract class Defect extends Event
{
    const
        EVENT_NAME = 'defect',
        WARNING = 'Warning',
        ERROR = 'Error';
    
    protected
        $node,
        $severity;
    
    public function __construct(Node $node, $severity = self::WARNING)
    {
        $this->node = $node;
        $this->setSeverity($severity);
    }
            
    public function getLine()
    {
        return $this->node->getLine();    
    }
    
    public function getSeverity()
    {
        return $this->severity;
    }
    
    public function setSeverity($severity)
    {
        $this->severity = $severity;
        
        return $this;
    }
    
    abstract public function getMessage();
}