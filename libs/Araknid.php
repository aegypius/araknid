<?php

class Araknid {
	const VERSION = "0.1";
	static $instance;
	protected $router;
	protected $request;

	public function __construct() {
		$this->request = Araknid_Request::getInstance();
		$this->router = Araknid_Router::getInstance();
	}

	public function getRouter() {
		return $this->router;
	}

	public function getRequest() {
		return $this->request;
	}

	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}

/* Autoloader -------------------------------------------------------------- */
if ((!defined('ARAKNID_AUTOLOAD')) || (ARAKNID_AUTOLOAD == true)) {
	function __autoload($className) {
		$classFile = join(DIRECTORY_SEPARATOR, array_map('ucfirst', explode('_', $className)))
			   . (!defined('PHP_EXT') ? '.php' : PHP_EXT);
		include $classFile;
	}
}

