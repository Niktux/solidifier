<?php

namespace Solidifier\Visitors;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Interface_;

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

class FluidSetters extends NodeVisitorAbstract
{
    private
        $currentClass,
        $currentMethod;
    
    public function beforeTraverse(array $nodes)
    {
        $this->currentClass = null;
        $this->currentMethod = null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_ || $node instanceof Trait_ || $node instanceof Interface_)
        {
            $this->currentClass = null;
        }
        elseif($node instanceof ClassMethod)
        {
            if($this->currentMethod instanceof FluidSetterState)
            {
                if($this->currentClass['type'] !== 'interface' && $this->currentMethod->isValid() === false)
                {
                    echo sprintf(
                        "WARNING method %s::%s does not follow fluid interface\n",
                        $this->currentClass['name'],
                        $this->currentMethod->currentMethod
                    );
                }
            }
            
            $this->currentMethod = null;
        }
    }
    
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_)
        {
            $this->currentClass = array(
                'name' => $node->name,
                'type' => 'class',
            );
        }
        elseif ($node instanceof Trait_)
        {
            $this->currentClass = array(
                'name' => $node->name,
                'type' => 'trait',
            );
        }
        elseif ($node instanceof Interface_)
        {
            $this->currentClass = array(
                'name' => $node->name,
                'type' => 'interface',
            );
        }
        elseif($node instanceof ClassMethod)
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
}