<?php

namespace Solidifier;

use PhpParser\Node;

abstract class Defect extends Event
{
    const
        EVENT_NAME = 'defect';    
    
    protected
        $node,
        $context;
    
    public function __construct(Node $node)
    {
        $this->node = $node;
        $this->context = null;
    }
            
    public function getLine()
    {
        return $this->node->getLine();    
    }
    
    public function getNode()
    {
        return $this->node;
    }

    public function getContext()
    {
        if($this->context instanceof Node)
        {
            return $this->context;
        }
        
        return $this->node;
    }
    
    public function setContext(Node $contextNode)
    {
        $this->context = $contextNode;
        
        return $this;
    }
    
    abstract public function getMessage();
}