<?php

namespace Solidifier\Visitors\Property;

use Solidifier\Visitors\AbstractClassVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;

class PublicAttributes extends AbstractClassVisitor
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
                    echo sprintf(
                        "WARNING property %s is public in %s %s\n",
                        $property->name,
                        $this->currentClass->type,
                        $this->currentClass->name
                    );
                }
            }   
        }
    }
}