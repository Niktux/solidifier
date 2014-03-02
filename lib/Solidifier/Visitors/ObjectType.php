<?php

namespace Solidifier\Visitors;

class ObjectType
{
    const
        TYPE_CLASS = 'class',
        TYPE_TRAIT = 'trait',
        TYPE_INTERFACE = 'interface';
    
    public
        $namespace,
        $name,
        $fullname,
        $type;
    
    public function __construct($namespace, $name, $type = self::TYPE_CLASS)
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->fullname = empty($namespace) ? $name : "$namespace\\$name";
        $this->type = $type;    
    }
}