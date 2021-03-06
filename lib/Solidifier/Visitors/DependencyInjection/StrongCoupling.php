<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;
use Solidifier\Visitors\ObjectType;

class StrongCoupling extends ContextualVisitor
{
    private
        $allowedClass,
        $excludePattern;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->allowedClass = array();
        $this->excludePattern = array();
    }
    
    public function addAllowedClass($fullyQualifiedClassName)
    {
        $this->allowedClass[] = $fullyQualifiedClassName;
        
        return $this;
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
                    return $this->enterNewName($node);
                }
            }
        }
    }
    
    private function enterNewName(New_ $node)
    {
        if($this->isCurrentClassAllowedToInstanciateObjects() === false)
        {
            if($this->isAnAllowedObjectType($node->class) === false)
            {
                $defect = new \Solidifier\Defects\StrongCoupling($this->currentObjectType, $node);
                $defect->setContext($this->nodeStack->top());
                $this->dispatch($defect);
            }
        }
    }
    
    private function isCurrentClassAllowedToInstanciateObjects()
    {
        return in_array($this->currentObjectType->fullname, $this->allowedClass);
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
