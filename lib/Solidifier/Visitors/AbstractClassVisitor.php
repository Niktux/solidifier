<?php

namespace Solidifier\Visitors;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Namespace_;
use Solidifier\Dispatcher;
use Solidifier\Defect;

abstract class AbstractClassVisitor extends NodeVisitorAbstract
{
    protected
        $dispatcher,
        $currentNamespace,
        $currentClass;
    
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    protected function dispatch(Defect $event)
    {
        return $this->dispatcher->dispatch($event);
    }
    
    public function beforeTraverse(array $nodes)
    {
        $this->currentNamespace = null;
        $this->currentClass = null;
    }
    
    public function enterNode(Node $node)
    {
        if($node instanceof Namespace_)
        {
            $this->currentNamespace = $node->name;
        }
        elseif($node instanceof Class_)
        {
            $this->currentClass = new ClassInfo($this->currentNamespace, $node->name);
        }
        elseif($node instanceof Interface_)
        {
            $this->currentClass = new ClassInfo($this->currentNamespace, $node->name, ClassInfo::TYPE_INTERFACE);
        }
        elseif($node instanceof Trait_)
        {
            $this->currentClass = new ClassInfo($this->currentNamespace, $node->name, ClassInfo::TYPE_TRAIT);
        }
    }
    
    public function leaveNode(Node $node)
    {
        if($node instanceof Namespace_)
        {
            $this->currentNamespace = null;
        }
        else if($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_)
        {
            $this->currentClass = null;
        }
    }
}