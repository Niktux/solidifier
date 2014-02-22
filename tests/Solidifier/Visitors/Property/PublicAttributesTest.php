<?php

namespace Solidifier\Visitors\Property;

use Solidifier\AnalyzeTestCase;
use Solidifier\Analyzer;

class PublicAttributesTest extends AnalyzeTestCase
{
    protected function configureAnalyzer(Analyzer $analyzer)
    {
        $analyzer->addVisitor('analyze', new PublicAttributes());
    }
    
    public function testFoo()
    {
        $files = array(
        	'foo.php' => '<?php class Foo { public $bar;}',
        );
        
        list($events, $types) = $this->analyze($files);
    
        $this->assertContains('Solidifier\Defects\PublicAttribute', $types);
    }
}