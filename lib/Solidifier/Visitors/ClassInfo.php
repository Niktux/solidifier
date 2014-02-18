<?php

namespace Solidifier\Visitors;

class ClassInfo
{
    const
        TYPE_CLASS = 'class',
        TYPE_TRAIT = 'trait',
        TYPE_INTERFACE = 'interface';
    
    public
        $namespace,
        $classname,
        $name,
        $type;
    
    public function __construct($namespace, $classname, $type = self::TYPE_CLASS)
    {
        $this->namespace = $namespace;
        $this->classname = $classname;
        $this->name = empty($namespace) ? $classname : "$namespace\\$classname";
        $this->type = $type;    
    }
}