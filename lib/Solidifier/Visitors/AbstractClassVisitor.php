<?php

namespace Solidifier\Visitors;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;

abstract class AbstractClassVisitor extends NodeVisitorAbstract
{
    protected
        $currentClass;
    
    public function beforeTraverse(array $nodes)
    {
        $this->currentClass = null;
    }
    
    public function enterNode(Node $node)
    {
        if($node instanceof Class_)
        {
            $this->currentClass = new ClassInfo($node->name);
        }
        elseif($node instanceof Interface_)
        {
            $this->currentClass = new ClassInfo($node->name, ClassInfo::TYPE_INTERFACE);
        }
        elseif($node instanceof Trait_)
        {
            $this->currentClass = new ClassInfo($node->name, ClassInfo::TYPE_TRAIT);
        }
    }
    
    public function leaveNode(Node $node)
    {
        if($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Trait_)
        {
            $this->currentClass = null;
        }
    }
}