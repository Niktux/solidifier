<?php

namespace Solidifier\Visitors\Encapsulation;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;

class PublicClass extends ContextualVisitor
{
    private
        $threshold,
        $minMethodCount,
        $methods;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->threshold = 0.50;
        $this->minMethodCount = 4;
    }
    
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;
        
        return $this;
    }
    
    public function setMinMethodCount($minMethodCount)
    {
        $this->minMethodCount = $minMethodCount;
        
        return $this;
    }
    
    protected function enter(Node $node)
    {
        if($node instanceof Class_)
        {
            $this->methods = array();
        }
        elseif($node instanceof ClassMethod)
        {
            $this->enterClassMethod($node);
        }
    }
    
    private function enterClassMethod(ClassMethod $node)
    {
        $methodName = $node->name;
        
        if($this->isGetterOrSetter($methodName) !== true && $this->isNeitherConstructorNorDestructor($methodName))
        {
            $this->methods[$methodName] = $node->isPublic();
        }
    }

    private function isGetterOrSetter($methodName)
    {
        $prefix = strtolower(substr($methodName, 0, 3));
    
        return $prefix === 'get' || $prefix === 'set';
    }  
    
    private function isNeitherConstructorNorDestructor($methodName)
    {
        $name = strtolower($methodName);
        
        return $name !== '__construct' && $name !== '__destruct' && $name !== strtolower($this->currentObjectType->name);
    }

    protected function leave(Node $node)
    {
        if($node instanceof Class_)
        {
            $nbMethods = count($this->methods);
            
            if($nbMethods >= $this->minMethodCount)
            {
                $nbPublicMethods = count(array_filter($this->methods));
                
                if(((float)$nbPublicMethods / (float)$nbMethods) >= $this->threshold)
                {
                    $this->dispatch(new \Solidifier\Defects\PublicClass($node));
                }
            }
        }
    }
}