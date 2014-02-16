<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local;
use Gaufrette\File;
use Solidifier\Visitors\Property\PublicAttributes;
use Solidifier\Visitors\GetterSetter\FluidSetters;

require 'vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 250);


$adapter = new Local('../karma/src');
$fs = new Filesystem($adapter);

foreach($fs->keys() as $key)
{
    if($adapter->isDirectory($key) === false)
    {
        parseFile($fs->get($key));
    }
}


function parseFile(File $file)
{
    $key = $file->getKey();
  //  echo "\n*** In file $key\n";
    
    $code = $file->getContent();
    
    $parser = new PhpParser\Parser(new PhpParser\Lexer());
    $traverser     = new PhpParser\NodeTraverser();
    
    $traverser->addVisitor(new PublicAttributes());
    $traverser->addVisitor(new FluidSetters());
    
    try
    {
        $stmts = $parser->parse($code);
        
        $traverser->traverse($stmts);
    }
    catch (PhpParser\Error $e)
    {
        echo 'Parse Error: ', $e->getMessage();
    }

}