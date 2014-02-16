<?php

namespace Solidifier\Visitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Property;

class PublicAttributes extends \PhpParser\NodeVisitorAbstract
{
    private 
        $currentClass;
    
    public function beforeTraverse(array $nodes)
    {
        $this->currentClass = null;
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
        if ($node instanceof Property)
        {
            if($node->isPublic())
            {
                foreach($node->props as $property)
                {
                    echo sprintf(
                        "WARNING property %s is public in %s %s\n",
                        $property->name,
                        $this->currentClass['type'],
                        $this->currentClass['name']
                    );
                }
            }   
        }
    }
    
    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_ || $node instanceof Trait_)
        {
            $this->currentClass = null;
        }    	
    }
    
    public function afterTraverse(array $nodes)
    {
    	
    }
}