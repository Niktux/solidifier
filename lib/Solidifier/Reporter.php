<?php

namespace Solidifier;

interface Reporter
{
    public function render(array $defects);
    public function save($reportFilename);
}