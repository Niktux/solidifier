<?php

namespace Solidifier\Visitors\GetterSetter;

use Solidifier\Visitors\AbstractClassVisitor;
use Solidifier\Visitors\ClassInfo;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use Solidifier\Defects\NotFluidSetter;

class FluidSetterState
{
    public
        $currentMethod,
        $returnCount,
        $returnThis;
    
    public function __construct($name)
    {
        $this->currentMethod = $name;
        $this->returnCount = 0;
        $this->returnThis = false;
    }
    
    public function isValid()
    {
        return $this->returnCount === 1 && $this->returnThis === true;
    }
}

class FluidSetters extends AbstractClassVisitor
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
                if($this->currentClass->type !== ClassInfo::TYPE_INTERFACE
                && $this->currentMethod->isValid() === false)
                {
                    $this->dispatch(
    	               new NotFluidSetter($this->currentClass, $node)
                    );
                }
            }
            
            $this->currentMethod = null;
        }
    }
}