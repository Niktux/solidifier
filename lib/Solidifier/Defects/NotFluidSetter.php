<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node\Stmt\ClassMethod;

class NotFluidSetter extends Defect
{
    private
        $objectType;
        
    public function __construct(ObjectType $objectType, ClassMethod $node)
    {
        parent::__construct($node);

        $this->objectType = $objectType;        
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Method <type>%s</type>::<id>%s</id>() does not follow fluid interface',
            $this->objectType->fullname,
            $this->node->name
        );
    }
}
