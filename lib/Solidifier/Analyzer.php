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
    
    public function analyze()
    {
        $adapter = $this->fs->getAdapter();
        
        $iterator = new \RegexIterator(
            new \ArrayIterator($this->fs->keys()), 
            '~.php$~'
        );
        
        foreach($iterator as $key)
        {
            if($adapter->isDirectory($key) === false)
            {
                $this->parseFile($this->fs->get($key));
            }
        }
    
        $this->dispatcher->dispatch(new TraverseEnd());
    }
    
    private function parseFile(File $file)
    {
        $key = $file->getKey();
        $code = $file->getContent();
    
        $parser = new Parser(new Lexer());
        $traverser = new NodeTraverser();
    
        $this->dispatcher->dispatch(new ChangeFile($file));
    
        $traverser->addVisitor(new PublicAttributes($this->dispatcher));
        $traverser->addVisitor(new FluidSetters($this->dispatcher));
        $traverser->addVisitor(new MagicalInstantiation($this->dispatcher));
    
        $visitor = new StrongCoupling($this->dispatcher);
        $visitor->addExcludePattern('~Iterator$~')
        ->addExcludePattern('~^Null~')
        ->addExcludePattern('~Exception$~');
    
        $traverser->addVisitor($visitor);
    
        $stmts = $parser->parse($code);
        $traverser->traverse($stmts);
    }    
}