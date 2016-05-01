<?php

require_once __DIR__ . '/../app/Autoloader.php';

set_include_path(implode(PATH_SEPARATOR, array(
	dirname(__FILE__),

	get_include_path(),
)));
