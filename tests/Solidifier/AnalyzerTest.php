<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Solidifier\Dispatcher\TestDispatcher;

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
        $this->analyzer->analyze();
        
        return $this->dispatcher->getEvents();
    }
    
    public function testFoo()
    {
        $events = $this->analyze();        
        
        $types = array_map(function ($event) {
        	return get_class($event);
        }, $events);
        
        $this->assertContains('Solidifier\Events\ChangeFile', $types);
        $this->assertContains('Solidifier\Events\TraverseEnd', $types);
        $this->assertContains('Solidifier\Defects\PublicAttribute', $types);
    }
}