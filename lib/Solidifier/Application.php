<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Solidifier\Command\Run;

class Application extends \Pimple
{
    public function __construct()
    {
        parent::__construct();
        
        $this->initializeServices();
        $this->initializeCommands();
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
            return new DefectDispatcher($c['event.dispatcher']);
        };
    }
    
    private function initializeCommands()
    {
        $this['run'] = $this->factory(function ($c) {
            return new Run($c['dispatcher'], $c['defect.subscriber']);	
        });
    }
}