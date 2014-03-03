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
            'Parameter <id>%s</id> is typed as %s <type>%s</type> (instead of using an interface) in method <method>%s()</method>',
            $this->node->name,
            $this->parameterType,
            $this->parameterTypeName,
            $this->method
        );
    }    
}