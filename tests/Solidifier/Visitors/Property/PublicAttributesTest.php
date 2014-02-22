<?php

namespace Solidifier\Visitors\Property;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class PublicAttributesTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\PublicAttribute';
    
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $analyzer->addVisitor('analyze', new PublicAttributes());
    }
    
    public function testSingle()
    {
        $this->analyze(array(
        	'foo.php' => '<?php class Foo { public $bar;}',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testMany()
    {
        $this->analyze(array(
        	'foo.php' => '<?php
            class Foo
            {
                public $bar;
                public $baz, $bad; 
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 3);
    }
    
    public function testNone()
    {
        $this->analyze(array(
        	'foo.php' => '<?php
            class Foo
            {
                protected $bar;
                private $baz, $bad; 
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
    
    public function testManyClasses()
    {
        $this->analyze(array(
        	'foo.php' => '<?php
            class Foo
            {
                protected $bar;
                public $baz, $bad; 
            }
            class Bar
            {
                public $bar;
                public $baz;
                public $bad;
                public function foo() { $foo = 2; } 
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 5);
    }
}