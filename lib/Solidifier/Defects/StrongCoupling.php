<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ClassInfo;
use PhpParser\Node\Expr\New_;

class StrongCoupling extends Defect
{
    private
        $classInfo;
        
    public function __construct(ClassInfo $classInfo, New_ $node)
    {
        parent::__construct($node, self::WARNING);
        
        $this->classInfo = $classInfo;
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Allocation in %s <info>%s</info> (instanciate %s)',
            $this->classInfo->type,
            $this->classInfo->name,
            $this->node->class
        );
    }
}
