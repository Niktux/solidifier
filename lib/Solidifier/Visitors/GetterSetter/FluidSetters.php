<?php

namespace Solidifier\Visitors\GetterSetter;

use Solidifier\Visitors\ContextualVisitor;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use Solidifier\Defects\NotFluidSetter;

class FluidSetters extends ContextualVisitor
{
    private
        $currentMethod;
    
    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);
        
        $this->currentMethod = null;
    }
    
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        // FIXME callmethod stack ...
        if($node instanceof ClassMethod)
        {
            if(strtolower(substr($node->name, 0, 3)) === 'set')
            {
                $this->currentMethod = new FluidSetterState($node->name);
            }
        }
        elseif($node instanceof Return_)
        {
            if($this->currentMethod instanceof FluidSetterState)
            {
                $this->currentMethod->returnCount++;
                
                if($node->expr instanceof Variable)
                {
                    if($node->expr->name === 'this')
                    {
                        $this->currentMethod->returnThis = true;
                    }        
                }                
            }
        }
    }
    
    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);
        
        if($node instanceof ClassMethod)
        {
            if($this->currentMethod instanceof FluidSetterState)
            {
                if($this->currentObjectType->type !== ObjectType::TYPE_INTERFACE
                && $this->currentMethod->isValid() === false)
                {
                    $this->dispatch(
                       new NotFluidSetter($this->currentObjectType, $node)
                    );
                }
            }
            
            $this->currentMethod = null;
        }
    }
}