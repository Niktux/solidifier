<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use PhpParser\Node;

class DependencyUponImplementation extends Defect
{
    private
        $parameterTypeName,
        $parameterType,
        $method;
    
    public function __construct(Node $node, $parameterTypeName, $parameterType, $method)
    {
        parent::__construct($node);
        
        $this->parameterTypeName = $parameterTypeName;
        $this->parameterType = $parameterType;
        $this->method = $method;
    }
    
    public function getMessage()
    {
        return sprintf(
            'Parameter <comment>%s</comment> is typed as %s <comment>%s</comment> (instead of using an interface) in method <info>%s()</info>',
            $this->node->name,
            $this->parameterType,
            $this->parameterTypeName,
            $this->method
        );
    }    
}