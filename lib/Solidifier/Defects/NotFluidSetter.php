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
        parent::__construct($node, self::WARNING);

        $this->objectType = $objectType;        
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Method <info>%s</info>::<comment>%s</comment>() does not follow fluid interface',
            $this->objectType->fullname,
            $this->node->name
        );
    }
}
