<?php

namespace Solidifier\Visitors;

class ClassInfo
{
    const
        TYPE_CLASS = 'class',
        TYPE_TRAIT = 'trait',
        TYPE_INTERFACE = 'interface';
    
    public
        $name,
        $type;
    
    public function __construct($name, $type = self::TYPE_CLASS)
    {
        $this->name = $name;
        $this->type = $type;    
    }
}