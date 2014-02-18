<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Visitors\AbstractClassVisitor;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use Solidifier\Visitors\ClassInfo;

class StrongCoupling extends AbstractClassVisitor
{
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        if($this->currentClass instanceof ClassInfo)
        {
            if($node instanceof New_)
            {
                echo sprintf(
                    "WARNING allocation in %s %s at line %d\n",
                    $this->currentClass->type,
                    $this->currentClass->name,
                    $node->getLine()
                );
            }
        }
    }
}