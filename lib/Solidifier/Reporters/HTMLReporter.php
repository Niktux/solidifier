<?php

namespace Solidifier\Reporters;

use PhpParser\PrettyPrinter\Standard;
use Solidifier\Reporter;

class HTMLReporter implements Reporter
{
    private
        $content,
        $twig;
    
    public function __construct(\Twig_Environment $twig)
    {
        $this->content = null;
        $this->twig = $twig;    
    }
    
    public function render(array $defects)
    {
        $prettyPrint = new Standard();
        
        $this->content = $this->twig->render(
           'report.html.twig',
            array(
                'project' => 'Solidifier',
                'defects' => $this->sortDefectsByNamespace($defects),
                'printer' => $prettyPrint,
        ));
        
        return $this;
    }
    
    private function sortDefectsByNamespace(array $defects)
    {
        $result = array();
        ksort($defects);
        
        foreach($defects as $file => $fileDefects)
        {
            $namespace = implode('/', explode('/', $file, -1));
            
            if(! isset($result[$namespace]))
            {
                $result[$namespace] = array();
            }
            
            $result[$namespace][$file] = $fileDefects;
        }
        
        return $result;
    }
    
    public function save($reportFilename)
    {
        file_put_contents($reportFilename, $this->content);
    }
}