<?php

namespace Solidifier\Reporters;

class HTMLReporter
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
        $this->content = $this->twig->render(
           'report.html.twig',
            array(
                'project' => 'Solidifier',
                'defects' => $defects
        ));
        
        return $this;
    }
    
    public function save($reportFilename)
    {
        file_put_contents($reportFilename, $this->content);
    }
}