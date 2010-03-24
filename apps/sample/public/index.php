<?php
/* --------------------------------------------------------------------
	CONSTANTS
-------------------------------------------------------------------- */
define('APP_PATH',        realpath('..'));
define('APP_CONFIG_PATH', realpath(APP_PATH . DIRECTORY_SEPARATOR . 'config'));
define('APP_MODELS_PATH', realpath(APP_PATH . DIRECTORY_SEPARATOR . 'models'));

set_include_path(
	realpath('../../../libs') . PATH_SEPARATOR .
	APP_MODELS_PATH . PATH_SEPARATOR .
	get_include_path()
);
/* --------------------------------------------------------------------
	BOOTSTRAP
-------------------------------------------------------------------- */
require_once 'Araknid.php';

Araknid::getInstance();
