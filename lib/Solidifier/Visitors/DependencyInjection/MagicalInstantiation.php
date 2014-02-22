<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Visitors\AbstractClassVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;

class MagicalInstantiation extends AbstractClassVisitor
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
