<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\Visitors\ContextualVisitor;
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
    
    public function beforeTraverse(array $nodes)
    {
        parent::beforeTraverse($nodes);
        
        $this->types = $this->objectTypes->getObjectTypes();
    }
    
    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        
        if($node instanceof Param)
        {
            if($this->currentMethod !== null)
            {
                $type = $node->type;
                
                if($type instanceof Name)
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
                            $this->dispatch(new DependencyUponImplementation(
                                $node,
                                $name,
                                $objectTypeType,
                                $this->currentMethod
                            ));
                        }
                    }
                }
            }
        }
    }
}