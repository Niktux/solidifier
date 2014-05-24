<?php

namespace Solidifier\Visitors\PreAnalyze;

use Solidifier\Parser\Visitors\ContextualVisitor;
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
        parent::__construct();
        
        $this->types = array();
    }
    
    protected function enter(Node $node)
    {
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
