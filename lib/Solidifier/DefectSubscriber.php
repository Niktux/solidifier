<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\File;

class DefectSubscriber implements EventSubscriberInterface
{
    private
        $counter,
        $currentFile,
        $output;
    
    public static function getSubscribedEvents()
    {
        return array(
            Defect::EVENT_NAME => array('onDefect'),
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
    
    public function setCurrentFile(File $file)
    {
       $this->currentFile = $file;

       return $this;
    }    
    
    public function onDefect(Defect $event)
    {
        $this->output->writeln(sprintf(
            "<fg=white;options=bold>%s @ l%d</fg=white;options=bold> : %s",
            $this->currentFile->getKey(),
            $event->getLine(),
            $event->getMessage()
        ));
        
        $this->counter++;
    }
    
    public function postMortemReport()
    {
        $this->output->writeln(sprintf(
        	'<comment>%d defect%s found</comment>',
            $this->counter,
            $this->counter > 0 ? 's' : ''
        ));    
    }
    
}