<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Gaufrette\Filesystem;
use Solidifier\Reporters\HTMLReporter;
use Symfony\Component\Yaml\Yaml;
use Gaufrette\Adapter\Local;
use Solidifier\Visitors\PreAnalyze\ObjectTypes;

class Application extends \Pimple
{
    public function __construct()
    {
        parent::__construct();
        
        $this->initializeFilesystem();
        $this->initializeServices();
        $this->initializeSubscribers();
    }
    
    private function initializeFilesystem()
    {
        $this['filesystem.path'] = 'src/';
        $this['filesystem.adapter'] = function($c) {
            return new Local($c['filesystem.path']);    
        };
        
        $this['filesystem'] = function($c) {
            return new Filesystem($c['filesystem.adapter']);    
        };
    }
    
    private function initializeServices()
    {
        $this['configuration'] = function($c) {
            
            $configuration = array();
            
            $filename = '.solidifier.yml';
            $fs = $c['filesystem'];
            
            if($fs->has($filename))
            {
                $configuration = Yaml::parse($fs->read($filename));
            }
            
            return $configuration;
        };
        
        $this['event.dispatcher'] = function($c) {
            return new EventDispatcher();
        };
        
        $this['dispatcher'] = function($c) {
            return new Dispatchers\EventDispatcher($c['event.dispatcher']);
        };
        
        $this['analyzer'] = function($c) {
            $analyzer = new Analyzers\Analyzer($c['dispatcher'], $c['filesystem']);

            $handler = new ConfigurationHandler($c['configuration'], $c['objectTypes.list']);
            $handler->configure($analyzer);
            
            return $analyzer;
        };
        
        $this['twig.path'] = 'views';
        $this['twig.cache'] = false;
        $this['twig'] = function($c) {
            $loader = new \Twig_Loader_Filesystem($c['twig.path']);
            return new \Twig_Environment($loader, array(
                'cache' => $c['twig.cache'],
            ));
        };
        
        $this['objectTypes.list'] = function($c) {
            return new ObjectTypes();    
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