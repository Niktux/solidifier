<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Gaufrette\Filesystem;

class Application extends \Pimple
{
    public function __construct()
    {
        parent::__construct();
        
        $this->initializeServices();
    }
    
    private function initializeServices()
    {
        $this['subscriber.console'] = function($c) {
            return new EventSubscribers\Console();
        };
        
        $this['event.dispatcher'] = function($c) {
            return new EventDispatcher();
        };
        
        $this['dispatcher'] = function($c) {
            return new Dispatcher\EventDispatcher($c['event.dispatcher']);
        };
        
        $this['analyzer'] = $this->protect(function(Filesystem $fs) {
            return new Analyzer($this['dispatcher'], $fs);    
        });
    }
}