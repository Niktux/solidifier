<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Solidifier\Command\Run;
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
        $this['defect.subscriber'] = function($c) {
            return new DefectSubscriber();
        };
        
        $this['event.dispatcher'] = function($c) {
            $dispatcher = new EventDispatcher();

            $dispatcher->addSubscriber($c['defect.subscriber']);
            
            return $dispatcher;
        };
        
        $this['dispatcher'] = function($c) {
            return new Dispatcher($c['event.dispatcher']);
        };
        
        $this['analyzer'] = $this->protect(function(Filesystem $fs) {
            return new Analyzer($this['dispatcher'], $fs);    
        });
    }
}