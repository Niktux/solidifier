<?php

namespace Solidifier;

use Solidifier\Visitors\Property\PublicAttributes;
use Solidifier\Visitors\GetterSetter\FluidSetters;
use Solidifier\Visitors\DependencyInjection\MagicalInstantiation;
use Solidifier\Visitors\DependencyInjection\StrongCoupling;
use Solidifier\Visitors\PreAnalyze\ObjectTypes;
use Solidifier\Visitors\DependencyInjection\InterfaceSegregation;

class ConfigurationHandler
{
    private
        $configuration,
        $objectTypesList,
        $visitors;
    
    public function __construct(array $configuration, ObjectTypes $objectTypesList)
    {
        $this->configuration = $configuration;
        $this->objectTypesList = $objectTypesList;    
        
        $this->initializeVisitors();
    }
    
    public function configure(VisitableAnalyzer $analyzer)
    {
        $this->addPreAnalyzeVisitors($analyzer);            
        $this->addAnalyzeVisitors($analyzer);            
    }
    
    private function addPreAnalyzeVisitors(VisitableAnalyzer $analyzer)
    {
        $traverseName = 'preAnalyze';
        
        // this visitor must not be disabled
        $analyzer->addVisitor($traverseName, $this->objectTypesList);
        
        $visitors = array();
        
        return $this->addVisitors($analyzer, $visitors, $traverseName);
    }
    
    private function initializeVisitors()
    {        
        $this->visitors = array(
                        
            'property.public' => function(array $config) {
                return new PublicAttributes();    
            },
            
            'getterSetter.fluid' => function(array $config) {
                return new FluidSetters();    
            },
            
            'dependency.magical' => function(array $config) {
                return new MagicalInstantiation();    
            },
            
            'dependency.strongCoupling' => function(array $config) {
                $visitor = new StrongCoupling();
                
                $visitor->addExcludePattern('~Iterator$~')
                    ->addExcludePattern('~^Null~')
                    ->addExcludePattern('~Exception$~');
                
                if(isset($config['excludePatterns']))
                {
                    foreach($config['excludePatterns'] as $pattern)
                    {
                        $visitor->addExcludePattern("~$pattern~");
                    }            
                }
                
               return $visitor;
            },
            
            'dependency.interfaceSegregation' => function(array $config) {
                return new InterfaceSegregation($this->objectTypesList);    
            },
        );
    }
    
    private function addAnalyzeVisitors(VisitableAnalyzer $analyzer)
    {
        return $this->addVisitors($analyzer, $this->visitors, 'analyze');
    }
    
    private function addVisitors(VisitableAnalyzer $analyzer, array $visitors, $traverse)
    {
        foreach($visitors as $key => $closure)
        {
            $config = array();
            if(isset($this->configuration[$key]))
            {
                $config = $this->configuration[$key];
            }
            
            if(! isset($config['enabled']) || $config['enabled'] === true)
            {
                $analyzer->addVisitor($traverse, $closure($config));
            }
        }
    }
}