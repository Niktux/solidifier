<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class MagicalInstantiationTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\MagicUsage';
    
    private
        $visitor;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->visitor = new MagicalInstantiation();
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
                public function bar()
                {
                    $class = "Baz";
                    $a = new $class();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testNoDefect()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Bar();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
}