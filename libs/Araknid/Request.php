<?php

class Araknid_Request {
	static $instance;

	protected $uri;
	protected $method;
	protected $data;

	public function __construct() {
		$this->uri 	= trim(trim($_SERVER['REQUEST_URI'], ' /'));
		$this->method 	= $_SERVER['REQUEST_METHOD'];
		if (strtoupper($this->method) === 'PUT')
			$this->data	= file_get_contents('php://input');
	}

	public function getUri() {
		return $this->uri;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getRawData() {
		return $this->data;
	}

	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}
