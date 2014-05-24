<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node\Stmt\Property;

class EncapsulationViolation extends Defect
{
    private
        $propertyName;
    
    public function __construct($propertyName, Property $node)
    {
        parent::__construct($node);
    
        $this->propertyName = $propertyName;
    }
    
    public function getMessage()
    {
        return sprintf(
            'Both public getter and setter on private property <id>%s</id> is discouraged (encapsulation violation) ',
            $this->propertyName
        );
    }
}
