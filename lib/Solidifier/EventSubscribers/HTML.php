<?php

namespace Solidifier\EventSubscribers;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Solidifier\Events\TraverseEnd;
use Solidifier\Events\ChangeFile;
use Solidifier\Defect;
use Solidifier\Reporters\HTMLReporter;

class HTML implements EventSubscriberInterface
{
    const
        DEFAULT_REPORT_FILENAME = 'report.html';
    
    private
        $reporter,
        $reportFilename,
        $currentFile;
    
    public function __construct(HTMLReporter $reporter)
    {
        $this->reporter = $reporter;
        $this->reportFilename = self::DEFAULT_REPORT_FILENAME;
        $this->currentFile = null;
    }
    
    public function setReportFilename($filename)
    {
        $this->reportFilename = $filename;
        
        return $this;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            Defect::EVENT_NAME => array('onDefect'),
            TraverseEnd::EVENT_NAME => array('postMortemReport'),
            ChangeFile::EVENT_NAME => array('setCurrentFile'),
        );
    }
    
    public function setCurrentFile(ChangeFile $event)
    {
        $this->currentFile = $event->getCurrentFile();

        return $this;
    }    
    
    public function onDefect(Defect $event)
    {
    }
    
    public function postMortemReport(TraverseEnd $event)
    {
    }
}