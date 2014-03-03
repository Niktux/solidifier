<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class StrongCouplingTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\StrongCoupling';
    
    private
        $visitor;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->visitor = new StrongCoupling();
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
                    $a = new Baz();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testNested()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Baz(new Bar());
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 2);
    }
    
    public function testAllowedClass()
    {
        $this->visitor->addAllowedClass('Foo');
        
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Baz(new Bar());
                }
            }

            class Baz 
            {
                public function boo()
                {
                    return new Foo();
                }
            }           
            ',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }
    
    public function testOutsideClass()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            $f = new Foo();                        
            class Foo
            {
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
    
    public function testMagical()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new $baz();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }   
    
    public function testExclude()
    {
        $this->visitor->addExcludePattern('~Baz$~');
        
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Bar();
                    $b = new Baz();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }   
    
    public function testNamespace()
    {
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Some\NameSpaced\Classname\Bar();
                    $b = new Baz();
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 2);
    }   
    
    public function testNamespaceAndExclude()
    {
        $this->visitor->addExcludePattern('~Iterator~');
        
        $this->analyze(array(
            'foo.php' => '<?php
            class Foo
            {
                public function bar()
                {
                    $a = new Some\NameSpaced\Classname\BarIterator();
                    $b = new Iterators\Baz();
                }
            }',
        ));
    
        // exclude pattern must only apply on classnames, not namespace
        $this->assertContainsType(self::DEFECT_TYPE, 1);
    }   
}