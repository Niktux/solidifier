<?php

namespace Solidifier\Visitors;

use PhpParser\NodeVisitorAbstract;
use Solidifier\Visitor;
use Solidifier\Dispatcher\NullDispatcher;
use Solidifier\Dispatcher;
use Solidifier\Defect;

abstract class AbstractVisitor extends NodeVisitorAbstract implements Visitor
{
    protected
        $dispatcher;
    
    public function __construct()
    {
        $this->dispatcher = new NullDispatcher();
    }
    
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        
        return $this;
    }
    
    protected function dispatch(Defect $event)
    {
        return $this->dispatcher->dispatch($event);
    }
}