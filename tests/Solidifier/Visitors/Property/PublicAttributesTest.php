<?php

namespace Solidifier\Visitors\Property;

use Solidifier\AnalyzeTestCase;

class PublicAttributesTest extends AnalyzeTestCase
{
    protected function getFiles()
    {
        return array(
        	'foo.php' => '<?php class Foo { public $bar;}',
        );
    }
    
    protected function configureAnalyzer()
    {
        $this->analyzer->addVisitor('analyze', new PublicAttributes());
    }
    
    public function testFoo()
    {
        list($events, $types) = $this->analyze();
    
        $this->assertContains('Solidifier\Defects\PublicAttribute', $types);
    }
}