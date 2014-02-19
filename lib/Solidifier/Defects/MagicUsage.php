<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use PhpParser\PrettyPrinter\Standard;

class MagicUsage extends Defect
{
    public function getMessage()
    {
        $formatter = new Standard();
        $code = $formatter->prettyPrint(array($this->node));
        
        return 'Magic usage : ' . $code;
    }
}
