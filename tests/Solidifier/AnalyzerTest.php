<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Solidifier\Dispatchers\TestDispatcher;
use Solidifier\Visitors\Property\PublicAttributes;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    private
        $dispatcher,
        $analyzer;
    
    protected function setUp()
    {
        $this->dispatcher = new TestDispatcher();

        $adapter = new InMemory();
        $adapter->write('toto.php', '<?php class Foo { public $bar;}');
        
        $this->analyzer = new Analyzer($this->dispatcher, new Filesystem($adapter));
    }
    
    private function analyze()
    {
        $this->analyzer->run();
        
        return $this->dispatcher->getEvents();
    }
    
    public function testFoo()
    {
        $this->analyzer->addVisitor('analyze', new PublicAttributes());
        $events = $this->analyze();        
        
        $types = array_map(function ($event) {
        	return get_class($event);
        }, $events);
        
        $this->assertContains('Solidifier\Events\ChangeFile', $types);
        $this->assertContains('Solidifier\Events\TraverseEnd', $types);
        $this->assertContains('Solidifier\Defects\PublicAttribute', $types);
    }
}