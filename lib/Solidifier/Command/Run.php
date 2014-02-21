<?php

namespace Solidifier\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Gaufrette\File;
use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use Solidifier\Visitors\Property\PublicAttributes;
use Solidifier\Visitors\GetterSetter\FluidSetters;
use Solidifier\Visitors\DependencyInjection\StrongCoupling;
use Solidifier\DefectDispatcher;
use Solidifier\DefectSubscriber;
use Solidifier\Visitors\DependencyInjection\MagicalInstantiation;

class Run extends Command
{
    private
        $dispatcher,
        $subcriber;
    
    public function __construct(DefectDispatcher $dispatcher, DefectSubscriber $subscriber)
    {
        parent::__construct();
        
        $this->dispatcher = $dispatcher;
        $this->subcriber = $subscriber;
    }
    
    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Check rules')
            ->addArgument('src', InputArgument::REQUIRED, 'sources to parse');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->subcriber->setOutput($output);
        
        $src = $input->getArgument('src');

        $adapter = new Local($src);
        $fs = new Filesystem($adapter);
        
        foreach($fs->keys() as $key)
        {
            if($adapter->isDirectory($key) === false)
            {
                $this->parseFile($fs->get($key));
            }
        }
        
        $this->subcriber->postMortemReport();
    }

    private function parseFile(File $file)
    {
        $key = $file->getKey();
        $code = $file->getContent();
    
        $parser = new Parser(new Lexer());
        $traverser = new NodeTraverser();
    
        $this->subcriber->setCurrentFile($file);
        $traverser->addVisitor(new PublicAttributes($this->dispatcher));
        $traverser->addVisitor(new FluidSetters($this->dispatcher));
        $traverser->addVisitor(new MagicalInstantiation($this->dispatcher));
        
        $visitor = new StrongCoupling($this->dispatcher);
        $visitor->addExcludePattern('~Iterator$~')
            ->addExcludePattern('~^Null~')
            ->addExcludePattern('~Exception$~');
        
        $traverser->addVisitor($visitor);
    
        try
        {
            $stmts = $parser->parse($code);
            $traverser->traverse($stmts);
        }
        catch (PhpParser\Error $e)
        {
            $output->writeln('Parse Error: ', $e->getMessage());
        }
    }
}