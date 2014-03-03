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
            if(strtolower(substr($node->name, 0, 3)) === 'set')
            {
                $this->currentMethodState = new FluidSetterState($node->name);
            }
        }
        elseif($node instanceof Return_)
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