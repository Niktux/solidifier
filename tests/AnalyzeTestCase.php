<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Solidifier\Dispatchers\TestDispatcher;

abstract class AnalyzeTestCase extends \PHPUnit_Framework_TestCase
{
    protected
        $dispatcher;
    
    protected function setUp()
    {
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
        
        $events = $this->dispatcher->getEvents();
        $types = $this->extractEventTypes($events);
        
        $this->assertContains('Solidifier\Events\ChangeFile', $types);
        $this->assertContains('Solidifier\Events\TraverseEnd', $types);
        
        return array($events, $types);
    }
    
    private function extractEventTypes(array $events)
    {
        return array_map(function ($event) {
            return get_class($event);
        }, $events);
    }
}