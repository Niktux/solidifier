<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;

class MagicalInstantiation extends ContextualVisitor
{
    protected function enter(Node $node)
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
