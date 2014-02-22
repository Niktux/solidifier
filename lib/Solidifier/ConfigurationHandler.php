<?php

namespace Solidifier;

use Puzzle\Configuration;
use Solidifier\Visitors\Property\PublicAttributes;
use Solidifier\Visitors\GetterSetter\FluidSetters;
use Solidifier\Visitors\DependencyInjection\MagicalInstantiation;
use Solidifier\Visitors\DependencyInjection\StrongCoupling;

class ConfigurationHandler
{
    private
        $config;
    
    public function __construct(Configuration $config)
    {
        $this->config = $config;    
    }
    
    public function configure(VisitableAnalyzer $analyzer)
    {
        $this->addPreAnalyzeVisitors($analyzer);            
        $this->addAnalyzeVisitors($analyzer);            
    }
    
    private function addPreAnalyzeVisitors(VisitableAnalyzer $analyzer)
    {
        $traverse = 'preAnalyze';
    }
    
    private function addAnalyzeVisitors(VisitableAnalyzer $analyzer)
    {
        $traverse = 'analyze';
        
        $analyzer->addVisitor($traverse, new PublicAttributes());
        $analyzer->addVisitor($traverse, new FluidSetters());
        $analyzer->addVisitor($traverse, new MagicalInstantiation());
        
        $visitor = new StrongCoupling();
        $visitor->addExcludePattern('~Iterator$~')
            ->addExcludePattern('~^Null~')
            ->addExcludePattern('~Exception$~');
        $analyzer->addVisitor($traverse, $visitor);        
    }
}