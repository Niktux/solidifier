<?php

namespace Solidifier\Analyzers;

use Solidifier\AnalyzeTestCase;
use Solidifier\Visitors\Encapsulation\PublicAttributes;

class AnalyzerTest extends AnalyzeTestCase
{
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $analyzer->addVisitor('traverseNameWhichDoesNotExist', new PublicAttributes());
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testError()
    {
        $this->analyze(array());    
    }
}