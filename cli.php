#!/usr/bin/php
<?php

require_once __DIR__ . '/app/Autoloader.php';

$cli = new Cli($argv);
$domain = $cli->getParameterValue('domain');
$maxImages = $cli->getParameterValue('maxImages');
$maxIterations = $cli->getParameterValue('maxIterations');

$imageSaver = new ImageSaver_Parser();
$imageSaver->setLog(new Logger_ConsoleLog());
$imageSaver
	->setMaxImages($maxImages)
	->setMaxIterations($maxIterations)
	->parse($domain);