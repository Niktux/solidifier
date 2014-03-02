<?php

namespace Solidifier\Visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Namespace_;
use Solidifier\Dispatcher;
use PhpParser\Node\Stmt\ClassMethod;

abstract class ContextualVisitor extends AbstractVisitor
{
    protected
        $currentNamespace,
        $currentObjectType,
        $currentMethod;

    public function beforeTraverse(array $nodes)
    {
        $this->currentNamespace = null;
        $this->currentObjectType = null;
        $this->currentMethod = null;
    }
    
    public function enterNode(Node $node)
    {
        if($node instanceof Namespace_)
        {
            $this->currentNamespace = $node->name;
        }
        elseif($node instanceof Class_)
        {
            $this->currentObjectType = new ObjectType($this->currentNamespace, $node->name);
        }
        elseif($node instanceof Interface_)
        {
            $this->currentObjectType = new ObjectType($this->currentNamespace, $node->name, ObjectType::TYPE_INTERFACE);
        }
        elseif($node instanceof Trait_)
        {
            $this->currentObjectType = new ObjectType($this->currentNamespace, $node->name, ObjectType::TYPE_TRAIT);
        }
        elseif($node instanceof ClassMethod)
        {
            $this->currentMethod = $node->name;
        }
    }
    
    public function leaveNode(Node $node)
    {
        if($node instanceof Namespace_)
        {
            $this->currentNamespace = null;
        }
        elseif($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_)
        {
            $this->currentObjectType = null;
        }
        elseif($node instanceof ClassMethod)
        {
            $this->currentMethod = null;
        }
    }
}