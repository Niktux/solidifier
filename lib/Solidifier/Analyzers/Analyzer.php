<?php

namespace Solidifier\Analyzers;

use Gaufrette\Filesystem;
use Solidifier\Events\TraverseEnd;
use Gaufrette\File;
use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use Solidifier\Events\ChangeFile;
use Solidifier\Dispatcher;
use Solidifier\VisitableAnalyzer;
use Solidifier\Visitor;

class Analyzer implements VisitableAnalyzer
{
    private
        $nodeTraversers,
        $dispatcher,
        $fs;
    
    public function __construct(Dispatcher $dispatcher, Filesystem $fs)
    {
        $this->nodeTraversers = array(
            'preAnalyze' => new NodeTraverser(),
            'analyze' => new NodeTraverser(),
        );
        
        $this->dispatcher = $dispatcher;
        $this->fs = $fs;
    }
    
    public function addVisitor($traverseName, Visitor $visitor)
    {
        if(! isset($this->nodeTraversers[$traverseName]))
        {
            throw new \RuntimeException("$traverseName is not a valid traverse step");
        }
        
        $visitor->setDispatcher($this->dispatcher);
        $this->nodeTraversers[$traverseName]->addVisitor($visitor);
        
        return $this;
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
        $this->traverse($nodes, $this->nodeTraversers['preAnalyze']);
    }
    
    private function analyze(array $nodes)
    {
        $this->traverse($nodes, $this->nodeTraversers['analyze']);
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