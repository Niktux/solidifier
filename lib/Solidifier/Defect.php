<?php

namespace Solidifier;

use PhpParser\Node;

abstract class Defect extends Event
{
    const
        EVENT_NAME = 'defect';    
    
    protected
        $node;
    
    public function __construct(Node $node)
    {
        $this->node = $node;
    }
            
    public function getLine()
    {
        return $this->node->getLine();    
    }
    
    public function getNode()
    {
        return $this->node;
    }
    
    abstract public function getMessage();
}