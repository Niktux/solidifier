<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node\Expr\New_;

class StrongCoupling extends Defect
{
    private
        $objectType;
        
    public function __construct(ObjectType $objectType, New_ $node)
    {
        parent::__construct($node);
        
        $this->objectType = $objectType;
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Allocation in %s <type>%s</type> (instanciate %s)',
            $this->objectType->type,
            $this->objectType->fullname,
            $this->node->class
        );
    }
}
