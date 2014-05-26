<?php

namespace Solidifier\Visitors\Encapsulation;

use Solidifier\Parser\Visitors\ContextualVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;

class EncapsulationViolation extends ContextualVisitor
{
    private
        $privateAttributes,
        $publicMethods;
    
    protected function enter(Node $node)
    {
        if($node instanceof Class_)
        {
            $this->privateAttributes = array();
            $this->publicMethods = array();
        } 
        elseif($node instanceof Property)
        {
            $this->enterProperty($node);
        } 
        elseif($node instanceof ClassMethod)
        {
            $this->enterClassMethod($node);
        }
    }
    
    private function enterProperty(Property $node)
    {
        if($node->isPrivate())
        {
            foreach($node->props as $property)
            {
                $this->privateAttributes[$property->name] = $node;
            }
        }
    }
    
    private function enterClassMethod(ClassMethod $node)
    {
        if($node->isPublic())
        {
            $methodName = $node->name;

            if($this->isGetterOrSetter($methodName))
            {
                $correspondingAttribute = strtolower(substr($methodName, 3));
                
                if(! isset($this->publicMethods[$correspondingAttribute]))
                {
                    $this->publicMethods[$correspondingAttribute] = array();
                }
                
                $this->publicMethods[$correspondingAttribute][] = $methodName;    
            }
        }
    }
    
    private function isGetterOrSetter($methodName)
    {
        $prefix = strtolower(substr($methodName, 0, 3));
        
        return $prefix === 'get' || $prefix === 'set';
    }
    
    protected function leave(Node $node)
    {
        if($node instanceof Class_)
        {
            foreach($this->privateAttributes as $attribute => $attributeNode)
            {
                $key = strtolower($attribute);
                if(isset($this->publicMethods[$key]))
                {
                    if(count($this->publicMethods[$key]) >= 2)
                    {
                        $this->dispatch(
                            new \Solidifier\Defects\EncapsulationViolation($attribute, $attributeNode)
                        );
                    }
                }
            }
        }   
    }
    
    protected function after(array $nodes)
    {
        
    }   
}