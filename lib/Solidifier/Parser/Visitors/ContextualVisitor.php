<?php

namespace Solidifier\Parser\Visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\ClassMethod;
use Solidifier\Visitors\ObjectType;

abstract class ContextualVisitor extends AbstractVisitor
{
    protected
        $nodeStack,
        $currentNamespace,
        $currentObjectType,
        $currentMethod;

    public function __construct()
    {
        parent::__construct();
        
        $this->nodeStack = new \SplStack();
    }
    
    final public function beforeTraverse(array $nodes)
    {
        $this->currentNamespace = null;
        $this->currentObjectType = null;
        $this->currentMethod = null;
        
        $this->before($nodes);
    }
    
    final public function enterNode(Node $node)
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
            $this->currentMethod = $node;
        }
        
        $this->enter($node);
        $this->nodeStack->push($node);
    }
    
    final public function leaveNode(Node $node)
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
        
        $this->leave($node);
        $this->nodeStack->pop();
    }
    
    final public function afterTraverse(array $nodes)
    {
        $this->after($nodes);
    }
    
    protected function before(array $nodes)
    {
        
    }
    
    protected function enter(Node $node)
    {
        
    }
    
    protected function leave(Node $node)
    {
        
    }
    
    protected function after(array $nodes)
    {
        
    }
}