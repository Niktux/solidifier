<?php

namespace Solidifier;

interface VisitableAnalyzer
{
    public function addVisitor($traverseName, Visitor $visitor);
}