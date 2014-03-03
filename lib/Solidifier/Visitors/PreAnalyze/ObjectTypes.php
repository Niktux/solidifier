<?php

namespace Solidifier\Visitors\PreAnalyze;

use Solidifier\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;

class ObjectTypes extends ContextualVisitor
{
    private
        $types;
    
    public function __construct()
    {
        $this->types = array();
    }
    
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        if($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_)
        {
            $this->types[$this->currentObjectType->fullname] = $this->currentObjectType->type;
        }
    }
    
    public function getObjectTypes()
    {
        return $this->types;
    }
}
