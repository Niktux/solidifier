<?php

namespace Solidifier\EventSubscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Solidifier\Events\TraverseEnd;
use Solidifier\Events\ChangeFile;
use Solidifier\Defect;

class Console implements EventSubscriberInterface
{
    private
        $counter,
        $currentFile,
        $output;
    
    public static function getSubscribedEvents()
    {
        return array(
            Defect::EVENT_NAME => array('onDefect'),
            TraverseEnd::EVENT_NAME => array('postMortemReport'),
            ChangeFile::EVENT_NAME => array('setCurrentFile'),
        );
    }

    public function __construct()
    {
        $this->output = new NullOutput();
        $this->currentFile = null;
        $this->counter = 0;
    }
    
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        
        return $this;
    }
    
    public function setCurrentFile(ChangeFile $event)
    {
        $this->currentFile = $event->getCurrentFile();

        return $this;
    }    
    
    public function onDefect(Defect $event)
    {
        $this->output->writeln(sprintf(
            "<fg=white;options=bold>%s @ l%d</fg=white;options=bold> : %s",
            $this->currentFile,
            $event->getLine(),
            $event->getMessage()
        ));
        
        $this->counter++;
    }
    
    public function postMortemReport(TraverseEnd $event)
    {
        $this->output->writeln(sprintf(
            '<comment>%d defect%s found</comment>',
            $this->counter,
            $this->counter > 0 ? 's' : ''
        ));    
    }
}