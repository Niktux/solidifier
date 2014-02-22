<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Gaufrette\Filesystem;
use Puzzle\Configuration;
use Solidifier\Reporters\HTMLReporter;

class Application extends \Pimple
{
    public function __construct()
    {
        parent::__construct();
        
        $this->initializeServices();
        $this->initializeSubscribers();
    }
    
    private function initializeServices()
    {
        $this['event.dispatcher'] = function($c) {
            return new EventDispatcher();
        };
        
        $this['dispatcher'] = function($c) {
            return new Dispatchers\EventDispatcher($c['event.dispatcher']);
        };
        
        $this['analyzer'] = $this->protect(function(Configuration $config, Filesystem $fs) {
            $analyzer = new Analyzer($this['dispatcher'], $fs);

            $handler = new ConfigurationHandler($config);
            $handler->configure($analyzer);
            
            return $analyzer;
        });
        
        $this['twig.path'] = 'views';
        $this['twig.cache'] = false;
        $this['twig'] = function($c) {
            $loader = new \Twig_Loader_Filesystem($c['twig.path']);
            return new \Twig_Environment($loader, array(
                'cache' => $c['twig.cache'],
            ));
        };
    }
    
    private function initializeSubscribers()
    {
        $this['subscriber.console'] = function($c) {
            return new EventSubscribers\Console();
        };
        
        $this['reporter.html'] = function($c) {
            return new Reporters\HTMLReporter($c['twig']);
        };
        
        $this['subscriber.html'] = function($c) {
            return new EventSubscribers\HTML($c['reporter.html']);
        };
    }
}