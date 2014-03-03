<?php

namespace Solidifier\Visitors\DependencyInjection;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;
use Solidifier\Visitors\PreAnalyze\ObjectTypes;

class InterfaceSegregationTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\DependencyUponImplementation';
    
    private
        $preVisitor,
        $visitor;
    
    protected function setUp()
    {
        parent::setUp();
    
        $this->preVisitor = new ObjectTypes();
        $this->visitor = new InterfaceSegregation($this->preVisitor);
    }
    
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $analyzer->addVisitor('preAnalyze', $this->preVisitor);
        $analyzer->addVisitor('analyze', $this->visitor);
    }
    
    public function testSimple()
    {
        $this->analyze(array(
            'foo.php' => '<?php

            namespace NFoo\Sub {
                class SubFoo{}
                interface ISubFoo{}
            }
                        
            namespace Other {
                class IFoo{}
            }
                        
            namespace NFoo {
                        
                class Bar {}
                class Baz {}
                interface IFoo {}
                
                class Foo
                {
                    public function doSomething(Bar $b) {}
                    public function computeSomething(Baz $b, IFoo $f) {}
                    public function processSomething(IFoo $f, Bar $b, IFoo $g, Baz $c) {}
                    public function matchSomething(IFoo $f, IFoo $g) {}
                    public function trySomething($a, $b, $c) {}
                    public function unsetSomething(\NFoo\IFoo $i, \Other\IFoo $cl) {}
                    public function subSomething(Sub\SubFoo $c, Sub\ISubFoo $i) {}
                }
            }',
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 6);
    }
}