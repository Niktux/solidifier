<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Visitors\ClassVisitor;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\New_;
use Solidifier\Visitors\ClassInfo;
use Solidifier\Dispatcher;

class StrongCoupling extends ClassVisitor
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
    
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        if($this->currentClass instanceof ClassInfo)
        {
            if($node instanceof New_)
            {
                if($node->class instanceof Name)
                {
                    if($this->isAnAllowedObjectType($node->class) === false)
                    {
                        $this->dispatch(
                            new \Solidifier\Defects\StrongCoupling($this->currentClass, $node)
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
