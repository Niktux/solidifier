<?php

namespace Solidifier\Visitors\GetterSetter;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class FluidSetterTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\NotFluidSetter';
    
    private
        $visitor;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->visitor = new FluidSetters();
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
                private $bar;
                        
                public function setBar(Bar $b)
                {
                    $this->bar = $bar;
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testNegative()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                // Not a named as a setter
                public function initializeBar(Bar $b)
                {
                    $this->bar = $bar;
                }
                        
                // Fluid setter
                public function setBar(Bar $b)
                {
                    $this->bar = $bar;
                    
                    return $this;
                }    

                // tricky name
                public function unsetBar() {}
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
    
    public function testMultipleReturnStatements()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                public function setBar(Bar $b)
                {
                    if($b->a === null)
                    {
                        return false;
                    }
                        
                    $this->bar = $bar;
                    
                    return $this;
                }                        
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testBadReturnStatements()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                public function setBar(Bar $b)
                {
                    $this->bar = $bar;
                    
                    return false;
                }                        
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testFakeReturnThis()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                private $bar;
                        
                public function setBaz($b)
                {
                    return $this->setBar($b);
                }                        
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
}