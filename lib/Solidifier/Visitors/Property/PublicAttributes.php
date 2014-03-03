<?php

namespace Solidifier\Visitors\Property;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;

class PublicAttributes extends ContextualVisitor
{
    protected function enter(Node $node)
    {
        if($node instanceof Property)
        {
            if($node->isPublic())
            {
                foreach($node->props as $property)
                {
                    $this->dispatch(
                        new \Solidifier\Defects\PublicAttribute($this->currentObjectType, $property->name, $node)
                    );
                }
            }   
        }
    }
}