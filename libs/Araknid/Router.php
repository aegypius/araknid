<?php

class Araknid_Router {
	static $instance;
	protected $routes;

	public function __construct() {
		$request = Araknid_Request::getInstance();
	}

	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}
