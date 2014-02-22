<?php

namespace Solidifier;

use Gaufrette\Filesystem;
use Solidifier\Events\TraverseEnd;
use Gaufrette\File;
use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use Solidifier\Events\ChangeFile;
use Solidifier\Visitors\Property\PublicAttributes;
use Solidifier\Visitors\GetterSetter\FluidSetters;
use Solidifier\Visitors\DependencyInjection\MagicalInstantiation;
use Solidifier\Visitors\DependencyInjection\StrongCoupling;

class Analyzer
{
    private
        $dispatcher,
        $fs;
    
    public function __construct(Dispatcher $dispatcher, Filesystem $fs)
    {
        $this->dispatcher = $dispatcher;
        $this->fs = $fs;
    }
    
    public function run()
    {
        $nodes = $this->parseFiles();
        
        $this->preAnalyze($nodes);
        $this->analyze($nodes);
        
        $this->dispatcher->dispatch(new TraverseEnd());
    }
    
    private function parseFiles()
    {
        $nodes = array();
        
        $adapter = $this->fs->getAdapter();
        
        $iterator = new \RegexIterator(
            new \ArrayIterator($this->fs->keys()), 
            '~.php$~'
        );
        
        foreach($iterator as $key)
        {
            if($adapter->isDirectory($key) === false)
            {
                $nodes[$key] = $this->parseFile($this->fs->get($key));
            }
        }
        
        return $nodes;
    }
    
    private function parseFile(File $file)
    {
        $parser = new Parser(new Lexer());
    
        return $parser->parse($file->getContent());
    }    
    
    private function preAnalyze(array $nodes)
    {
        $traverser = new NodeTraverser();
        
        $this->traverse($nodes, $traverser);
    }
    
    private function analyze(array $nodes)
    {
        $traverser = new NodeTraverser();
    
        $traverser->addVisitor(new PublicAttributes($this->dispatcher));
        $traverser->addVisitor(new FluidSetters($this->dispatcher));
        $traverser->addVisitor(new MagicalInstantiation($this->dispatcher));

        $visitor = new StrongCoupling($this->dispatcher);
        $visitor->addExcludePattern('~Iterator$~')
          ->addExcludePattern('~^Null~')
          ->addExcludePattern('~Exception$~');
        $traverser->addVisitor($visitor);
        
        $this->traverse($nodes, $traverser);
    }
    
    private function traverse(array $nodes, NodeTraverser $traverser)
    {
        foreach($nodes as $file => $stmts)
        {
            $this->dispatcher->dispatch(new ChangeFile($file));
            $traverser->traverse($stmts);
        }
    }
}