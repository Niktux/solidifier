<?php

namespace Solidifier\Visitors\Encapsulation;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzers\Analyzer;

class PublicClassesTest extends AnalyzeTestCase
{
    const
        DEFECT_TYPE = 'Solidifier\Defects\PublicClass';
    
    private
        $threshold,
        $minMethodCount;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->threshold = 0.50;
        $this->minMethodCount = 4;
    }
    
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $visitor = (new PublicClass())
            ->setThreshold($this->threshold)
            ->setMinMethodCount($this->minMethodCount);
        
        $analyzer->addVisitor('analyze', $visitor);
    }
    
    /**
     * @dataProvider providerTestSimple
     */
    public function testSimple($threshold, $minMethodCount, $expected)
    {
        $this->threshold = $threshold;
        $this->minMethodCount = $minMethodCount;
        
        $this->analyze(array(
            'foo.php' => <<< PHP
<?php

class Foo
{
    // constructor has to be ignored
    public function __construct() {}
                        
    public function run() {}
    public function do_something() {}
    public function do_stuff() {}
    private function bar() {}
    private function baz() {}
}
PHP
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, $expected);
    }
    
    public function providerTestSimple()
    {
        return array(
            array(0.25, 1, 1),
            array(0.50, 1, 1),
            array(0.60, 1, 1),
            array(0.65, 1, 0),
            array(0.75, 1, 0),
            array(1.00, 1, 0),
            
            array(0.25, 5, 1),
            array(0.50, 5, 1),
            array(0.60, 5, 1),
            array(0.65, 5, 0),
            array(0.75, 5, 0),
            array(1.00, 5, 0),
            
            array(0.25, 6, 0),
            array(0.50, 6, 0),
            array(0.60, 6, 0),
            array(0.65, 6, 0),
            array(0.75, 6, 0),
            array(1.00, 6, 0),
        );    
    }
    
    /**
     * @dataProvider providerTestSpecialCases
     */
    public function testSpecialCases($threshold, $minMethodCount)
    {
        $this->threshold = $threshold;
        $this->minMethodCount = $minMethodCount;
        
        $this->analyze(array(
            'foo.php' => <<< PHP
<?php

class Foo
{
    public function __construct() {}
    public function __destruct() {}
    public function getBar() {}
    public function setBar() {}
    private function bar() {}
    private function baz() {}
}
PHP
        ));
    
        $this->assertContainsType(self::DEFECT_TYPE, 0);
    }
    
    public function providerTestSpecialCases()
    {
        return array(
            array(0.10, 1),
            array(0.25, 1),
            array(0.50, 1),
            array(0.75, 1),
            array(1.00, 1),                        
        );    
    }
}