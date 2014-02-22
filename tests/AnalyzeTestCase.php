<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Solidifier\Dispatchers\TestDispatcher;

abstract class AnalyzeTestCase extends \PHPUnit_Framework_TestCase
{
    protected
        $events,
        $types,
        $dispatcher;
    
    protected function setUp()
    {
        $this->events = array();
        $this->types = array();
        $this->dispatcher = new TestDispatcher();
    }
    
    protected function configureAnalyzer(Analyzer $analyzer)
    {
    }
    
    protected function analyze(array $files)
    {
        $adapter = new InMemory($files);
        $analyzer = new Analyzer($this->dispatcher, new Filesystem($adapter));
        $this->configureAnalyzer($analyzer);
        
        $analyzer->run();
        
        $this->events = $this->dispatcher->getEvents();
        $this->types = $this->extractEventTypes($this->events);
        
        $this->assertContains('Solidifier\Events\ChangeFile', $this->types);
        $this->assertContains('Solidifier\Events\TraverseEnd', $this->types);
    }
    
    private function extractEventTypes(array $events)
    {
        return array_map(function ($event) {
            return get_class($event);
        }, $events);
    }
    
    protected function assertContainsType($type, $expectedCount)
    {
        $it = new \CallbackFilterIterator(new \ArrayIterator($this->types), function($item) use($type) {
        	return $item === $type;
        });
        
        return $this->assertSame($expectedCount, iterator_count($it));
    }
}