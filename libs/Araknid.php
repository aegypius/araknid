<?php
defined('PHP_EXT') or define('PHP_EXT', '.' . pathinfo(__FILE__, PATHINFO_EXTENSION));

class Araknid {
	const VERSION = "0.1";
/* --------------------------------------------------------------------
  Protected:
-------------------------------------------------------------------- */
	protected $router;
	protected $request;

/* --------------------------------------------------------------------
  Public:
-------------------------------------------------------------------- */
	public function __construct() {
		debug('Initializing '.__CLASS__);
		$this->request = Araknid_Request::getInstance();
		$this->router  = Araknid_Router::getInstance();
	}

	public function getRouter() {
		return $this->router;
	}

	public function getRequest() {
		return $this->request;
	}

/* --------------------------------------------------------------------
  Static:
-------------------------------------------------------------------- */
	static $instance;
	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}

/* Autoloader ------------------------------------------------------ */
if ((!defined('ARAKNID_AUTOLOAD')) || (ARAKNID_AUTOLOAD == true)) {
	function __autoload($className) {
		$classFile = join(DIRECTORY_SEPARATOR, array_map('ucfirst', explode('_', $className)))
			   . (!defined('PHP_EXT') ? '.php' : PHP_EXT);
		@include $classFile;
	}

	/* Initializing Log System ------------------------------------- */
	Araknid_Log::getInstance();
}


