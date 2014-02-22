<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Solidifier\Dispatchers\TestDispatcher;

abstract class AnalyzeTestCase extends \PHPUnit_Framework_TestCase
{
    protected
        $dispatcher,
        $analyzer;
    
    protected function setUp()
    {
        $this->dispatcher = new TestDispatcher();

        $adapter = new InMemory($this->getFiles());
        $this->analyzer = new Analyzer($this->dispatcher, new Filesystem($adapter));
        $this->configureAnalyzer();
    }
    
    abstract protected function getFiles();
    
    protected function configureAnalyzer()
    {
    }
    
    protected function analyze()
    {
        $this->analyzer->run();
        
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