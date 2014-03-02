<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node\Stmt\Property;

class PublicAttribute extends Defect
{
    private
        $objectType,
        $property;
        
    public function __construct(ObjectType $objectType, $property, Property $node)
    {
        parent::__construct($node, self::WARNING);

        $this->objectType = $objectType;    
        $this->property = $property;    
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Public property <comment>%s</comment> in %s <info>%s</info>',
            $this->property,
            $this->objectType->type,
            $this->objectType->fullname
        );
    }
}
