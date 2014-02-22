<?php

namespace Solidifier\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Solidifier\Application;
use Puzzle\Configuration\Memory;

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
            ->addArgument('src', InputArgument::REQUIRED, 'sources to parse');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dispatcher = $this->container['event.dispatcher'];
        
        $cli = $this->container['subscriber.console'];
        $cli->setOutput($output);
        $dispatcher->addSubscriber($cli);
        
        $src = $input->getArgument('src');
        $fs = new Filesystem(new Local($src));
        $config = new Memory(array());

        $analyzer = $this->container['analyzer']($config, $fs);
        $analyzer->run();
    }
}