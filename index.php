<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Solidifier\Command\Run;

ini_set('xdebug.max_nesting_level', 250);

$container = new Solidifier\Application();

$app = new Application();
$app->add($container['run']);
$app->run();