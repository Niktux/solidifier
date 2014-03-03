<?php

namespace Solidifier\Visitors\GetterSetter;

class FluidSetterState
{
    public
        $currentMethod,
        $returnCount,
        $returnThis;

    public function __construct($name)
    {
        $this->currentMethod = $name;
        $this->returnCount = 0;
        $this->returnThis = false;
    }

    public function isValid()
    {
        return $this->returnCount === 1 && $this->returnThis === true;
    }
}