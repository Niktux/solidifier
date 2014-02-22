<?php

namespace Solidifier\Analyzers;

use Solidifier\VisitableAnalyzer;
use Solidifier\Visitor;

class FakeAnalyzer implements VisitableAnalyzer
{
    public function __construct()
    {
        $this->visitors = array();    
    }
    
    public function addVisitor($traverseName, Visitor $visitor)
    {
        if(! isset($this->visitors[$traverseName]))
        {
            $this->visitors[$traverseName] = array();
        }
        
        $this->visitors[$traverseName][] = $visitor;
        
        return $this;
    }
    
    public function getVisitors()
    {
        return $this->visitors;
    }
}