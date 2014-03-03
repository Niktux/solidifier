<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Parser\Visitors\ContextualVisitor;
use Solidifier\Visitors\PreAnalyze\ObjectTypes;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Name;
use Solidifier\Visitors\ObjectType;
use Solidifier\Defects\DependencyUponImplementation;

class InterfaceSegregation extends ContextualVisitor
{
    private
        $objectTypes,
        $types;
    
    public function __construct(ObjectTypes $objectTypes)
    {
        parent::__construct();
        
        $this->objectTypes = $objectTypes;
        $this->types = array();
    }    
    
    public function before(array $nodes)
    {
        $this->types = $this->objectTypes->getObjectTypes();
    }
    
    protected function enter(Node $node)
    {
        if($node instanceof Param)
        {
            if($this->currentMethod !== null)
            {
                if($node->type instanceof Name)
                {
                    $this->checkIfTypeIsAnInterface($node, $node->type);
                }
            }
        }
    }
    
    private function checkIfTypeIsAnInterface(Node $node, Name $type)
    {
        $name = (string) $type;
        
        if($type->isFullyQualified() === false)
        {
            $name = $this->currentNamespace . '\\' . $name;
        }
        
        if(isset($this->types[$name]))
        {
            $objectTypeType= $this->types[$name];
            
            if($objectTypeType !== ObjectType::TYPE_INTERFACE)
            {
                $defect = new DependencyUponImplementation(
                    $node,
                    $name,
                    $objectTypeType,
                    $this->currentMethod->name
                );
                
                $defect->setContext($this->currentMethod);
                
                $this->dispatch($defect);
            }
        }
    }
}