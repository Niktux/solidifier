<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ClassInfo;
use PhpParser\Node\Stmt\ClassMethod;

class NotFluidSetter extends Defect
{
    private
        $classInfo;
        
    public function __construct(ClassInfo $classInfo, ClassMethod $node)
    {
        parent::__construct($node, self::WARNING);

        $this->classInfo = $classInfo;        
    }    
    
    public function getMessage()
    {
        return sprintf(
            'Method <info>%s</info>::<comment>%s</comment>() does not follow fluid interface',
            $this->classInfo->name,
            $this->node->name
        );
    }
}
