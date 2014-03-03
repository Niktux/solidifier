<?php

namespace Solidifier\Visitors\GetterSetter;

use Solidifier\Parser\Visitors\ContextualVisitor;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use Solidifier\Defects\NotFluidSetter;

class FluidSetters extends ContextualVisitor
{
    private
        $currentMethodState;
    
    protected function before(array $nodes)
    {
        $this->currentMethodState = null;
    }
    
    protected function enter(Node $node)
    {
        if($node instanceof ClassMethod)
        {
            return $this->enterClassMethod($node);
        }
        
        if($node instanceof Return_)
        {
            return $this->enterReturn($node);
        }
    }
    
    private function enterClassMethod(ClassMethod $node)
    {
        if($this->isASetter($node->name))
        {
            $this->currentMethodState = new FluidSetterState($node->name);
        }
    }
    
    private function isASetter($methodName)
    {
        return strtolower(substr($methodName, 0, 3)) === 'set';
    }
    
    private function enterReturn(Return_ $node)
    {
        if($this->currentMethodState instanceof FluidSetterState)
        {
            $this->currentMethodState->returnCount++;
            
            if($node->expr instanceof Variable)
            {
                if($node->expr->name === 'this')
                {
                    $this->currentMethodState->returnThis = true;
                }        
            }                
        }
    }
    
    protected function leave(Node $node)
    {
        if($node instanceof ClassMethod)
        {
            if($this->currentMethodState instanceof FluidSetterState)
            {
                if($this->currentObjectType->type !== ObjectType::TYPE_INTERFACE
                && $this->currentMethodState->isValid() === false)
                {
                    $this->dispatch(
                       new NotFluidSetter($this->currentObjectType, $node)
                    );
                }
            }
            
            $this->currentMethodState = null;
        }
    }
}