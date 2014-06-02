<?php

namespace Solidifier\Defects;

use Solidifier\Defect;

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
