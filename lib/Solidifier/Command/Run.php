<?php

namespace Solidifier\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Solidifier\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Run extends Command
{
    private
        $container;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->container = new Application();
    }
    
    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Check rules')
            ->addArgument('src', InputArgument::REQUIRED, 'sources to parse')
            ->addOption('htmlReport', null, InputOption::VALUE_REQUIRED, 'HTML report filename');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configureOutputs($input, $output);
        
        $this->container['filesystem.path'] = $input->getArgument('src');
        
        $analyzer = $this->container['analyzer'];
        $analyzer->run();
    }
    
    private function configureOutputs(InputInterface $input, OutputInterface $output)
    {
        $dispatcher = $this->container['event.dispatcher'];

        $this->enableConsoleOutput($dispatcher, $output);
        $this->enableHtmlReport($dispatcher, $input->getOption('htmlReport'));
    }
    
    private function enableConsoleOutput(EventDispatcherInterface $dispatcher, OutputInterface $output)
    {
        $console = $this->container['subscriber.console'];
        $console->setOutput($output);
        $dispatcher->addSubscriber($console);
    }
    
    private function enableHtmlReport(EventDispatcherInterface $dispatcher, $htmlReportFilename)
    {
        if($htmlReportFilename !== null)
        {
            $html = $this->container['subscriber.html'];
            $html->setReportFilename($htmlReportFilename);
            
            $dispatcher->addSubscriber($html);
        }
    }
}