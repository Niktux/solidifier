<?php

namespace Solidifier\Visitors\Property;

use Solidifier\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;

class PublicAttributes extends ContextualVisitor
{
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        if($node instanceof Property)
        {
            if($node->isPublic())
            {
                foreach($node->props as $property)
                {
                    $this->dispatch(
                        new \Solidifier\Defects\PublicAttribute($this->currentClass, $property->name, $node)
                    );
                }
            }   
        }
    }
}