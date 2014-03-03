<?php

namespace Solidifier;

use Solidifier\Analyzers\FakeAnalyzer;
use Solidifier\Visitors\PreAnalyze\ObjectTypes;

class ConfigurationHandlerTest extends \PHPUnit_Framework_TestCase
{
    private
        $analyzer;
    
    protected function setUp()
    {
        $this->analyzer = new FakeAnalyzer();    
    }
    
    private function getVisitorTypes()
    {
        $types = array();
        
        foreach($this->analyzer->getVisitors() as $traverse => $traverseVisitors)
        {
             foreach($traverseVisitors as $visitor)
             {
                 $types[] = get_class($visitor);
             }   
        }
        
        return $types;
    }
    
    private function assertHasVisitor($visitorClass)
    {
        return $this->assertContains($visitorClass, $this->getVisitorTypes());
    }
    
    private function assertNotHasVisitor($visitorClass)
    {
        return $this->assertNotContains($visitorClass, $this->getVisitorTypes());
    }
    
    public function testPassThru()
    {
        $handler = new ConfigurationHandler(array(), new ObjectTypes());
        $handler->configure($this->analyzer);
        
        $this->assertHasVisitor('Solidifier\Visitors\Property\PublicAttributes');
    }
    
    public function testEnable()
    {
        $values = array(
            'property.public' => array('enabled' => false),	
            'dependency.strongCoupling' => array('enabled' => true, 'excludePatterns' => array('foo')),	
        );
        
        $handler = new ConfigurationHandler($values, new ObjectTypes());
        $handler->configure($this->analyzer);     

        $this->assertNotHasVisitor('Solidifier\Visitors\Property\PublicAttributes');
        $this->assertHasVisitor('Solidifier\Visitors\DependencyInjection\StrongCoupling');
    }
}