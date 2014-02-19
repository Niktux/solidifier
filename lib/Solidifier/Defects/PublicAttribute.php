<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ClassInfo;
use PhpParser\Node\Stmt\Property;

class PublicAttribute extends Defect
{
    private
        $classInfo,
        $property;
        
    public function __construct(ClassInfo $classInfo, $property, Property $node)
    {
        parent::__construct($node, self::WARNING);

        $this->classInfo = $classInfo;    
        $this->property = $property;    
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Public property <comment>%s</comment> in %s <info>%s</info>',
            $this->property,
            $this->classInfo->type,
            $this->classInfo->name
        );
    }
}
