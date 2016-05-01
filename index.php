<?php

require_once __DIR__ . '/app/Autoloader.php';

$imageSaver = new ImageSaver_Parser();
$imageSaver
	->setMaxIterations(3)
	->parse('jeka.by');