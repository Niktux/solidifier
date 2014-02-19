<?php

namespace Solidifier;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Gaufrette\File;

class DefectSubscriber implements EventSubscriberInterface
{
    private
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
            "<comment>[%s]</comment> <fg=white;options=bold>%s @ l%d</fg=white;options=bold> : %s",
            $event->getSeverity(),
            $this->currentFile->getKey(),
            $event->getLine(),
            $event->getMessage()
        ));
    }
}