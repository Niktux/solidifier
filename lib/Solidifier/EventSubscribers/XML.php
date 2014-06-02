<?php

namespace Solidifier\EventSubscribers;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Solidifier\Events\TraverseEnd;
use Solidifier\Events\ChangeFile;
use Solidifier\Defect;
use Solidifier\Reporter;

class XML implements EventSubscriberInterface
{
    const
        DEFAULT_REPORT_FILENAME = 'report.xml';
    
    private
        $defects,
        $reporter,
        $reportFilename,
        $currentFile;
    
    public function __construct(Reporter $reporter)
    {
        $this->defects = array();
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
        if(! isset($this->defects[$this->currentFile]))
        {
            $this->defects[$this->currentFile] = array();
        }
        
        $this->defects[$this->currentFile][] = $event;
    }
    
    public function postMortemReport(TraverseEnd $event)
    {
        $this->reporter
            ->render($this->defects)
            ->save($this->reportFilename);
    }
}