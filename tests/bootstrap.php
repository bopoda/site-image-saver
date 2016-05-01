<?php

require_once __DIR__ . '/../app/Autoloader.php';
require 'DatabaseTestCase.php';

set_include_path(implode(PATH_SEPARATOR, array(
	dirname(__FILE__),

	get_include_path(),
)));
