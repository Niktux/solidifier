<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Visitors\ClassVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;

class MagicalInstantiation extends ClassVisitor
{
    public function enterNode(Node $node)
    {
        if($node instanceof New_)
        {
            if(! $node->class instanceof Name)
            {
                $this->dispatch(
                    new \Solidifier\Defects\MagicUsage($node)
                );
            }
        }
    }
}
