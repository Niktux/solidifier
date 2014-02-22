<?php

namespace Solidifier;

use PhpParser\NodeVisitor;

interface Visitor extends NodeVisitor
{
    public function setDispatcher(Dispatcher $dispatcher);
}