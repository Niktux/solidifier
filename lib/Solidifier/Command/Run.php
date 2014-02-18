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

class Run extends Command
{
    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Check rules')
            ->addArgument('src', InputArgument::REQUIRED, 'sources to parse');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
    }

    private function parseFile(File $file)
    {
        $key = $file->getKey();
        $code = $file->getContent();
    
        $parser = new Parser(new Lexer());
        $traverser = new NodeTraverser();
    
        $traverser->addVisitor(new PublicAttributes());
        $traverser->addVisitor(new FluidSetters());
        $traverser->addVisitor(new StrongCoupling());
    
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