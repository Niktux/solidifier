<?php

namespace Solidifier\Visitors\GetterSetter;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class EncapsulationViolationTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\EncapsulationViolation';
    
    private
        $visitor;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->visitor = new EncapsulationViolation();
    }
    
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $analyzer->addVisitor('analyze', $this->visitor);
    }
    
    public function testSimple()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $barBar;
                        
                public function setBarBar(BarBar $b)
                {
                    $this->barBar = $b;
                }
                        
                public function getBarBar()
                {
                    return $this->barBar;
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testGroupedProperties()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private
                    $foo,
                    $bar,
                    $baz;
                        
                public function setFoo(Bar $b)
                {
                    $this->foo = $b;
                }
                        
                public function setBar(Bar $b)
                {
                    $this->bar = $b;
                }
                        
                public function getBar()
                {
                    return $this->bar;
                }
                        
                public function getBaz()
                {
                    return $this->baz;
                }
                        
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testNoViolation()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                private function setBar(Bar $b)
                {
                    $this->bar = $b;
                }
                        
                public function getBar()
                {
                    return $this;
                }    
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
    
    public function testNoViolationTricky()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                public function setBar(Bar $b)
                {
                    $this->bar = $b;
                }
            }
                        
            class Other
            {
                public function getBar()
                {
                    return $this;
                }    
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
}