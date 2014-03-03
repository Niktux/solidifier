<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;
use Solidifier\Visitors\ObjectType;
use Solidifier\Dispatcher;

class StrongCoupling extends ContextualVisitor
{
    private
        $excludePattern;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->excludePattern = array();
    }
    
    public function addExcludePattern($regex)
    {
        $this->excludePattern[] = $regex;
        
        return $this;
    }
    
    protected function enter(Node $node)
    {
        if($this->currentObjectType instanceof ObjectType)
        {
            if($node instanceof New_)
            {
                if($node->class instanceof Name)
                {
                    if($this->isAnAllowedObjectType($node->class) === false)
                    {
                        $this->dispatch(
                            new \Solidifier\Defects\StrongCoupling($this->currentObjectType, $node)
                        );
                    }
                }
            }
        }
    }
    
    private function isAnAllowedObjectType($objectType)
    {
        $allowed = false;

        $objectType = $this->extractClassNameFromFullName($objectType);

        foreach($this->excludePattern as $pattern)
        {
            if(preg_match($pattern, $objectType))
            {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }
    
    private function extractClassNameFromFullName($classFullname)
    {
        $classname = $classFullname;
        
        $lastNamespaceDelimiterPosition = strrpos($classFullname, '\\');
        if($lastNamespaceDelimiterPosition !== false)
        {
            $classname = substr($classFullname, $lastNamespaceDelimiterPosition + 1);
        }

        return $classname;
    }
}
