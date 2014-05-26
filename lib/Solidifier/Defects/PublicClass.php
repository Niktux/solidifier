<?php

namespace Solidifier\Defects;

use Solidifier\Defect;
use Solidifier\Visitors\ObjectType;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Class_;

class PublicClass extends Defect
{
    public function getMessage()
    {
        return sprintf(
            'Class <id>%s</id> is mainly public : maybe it is over responsible',
            $this->node->name
        );
    }
}
