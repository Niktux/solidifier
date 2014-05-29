<?php

namespace Solidifier\Reporters;

use Solidifier\Reporter;

class XMLReporter implements Reporter
{
    private
        $dom;
    
    public function __construct()
    {
        $this->dom = new \DOMDocument('1.0', 'utf-8');
    }
    
    public function render(array $defects)
    {
        $root = $this->dom->createElement('project');
        $this->dom->appendChild($root);
        
        $node = $this->dom->createElement('defects', $this->countDefects($defects));
        $root->appendChild($node);
        
        return $this;
    }
    
    private function countDefects($defects)
    {
        $count = 0;
        
        foreach($defects as $fileDefects)
        {
            $count += count($fileDefects);
        }
        
        return $count;
    }

    public function save($reportFilename)
    {
        file_put_contents($reportFilename, $this->dom->saveXML());
    }
}