<?php

class Araknid_Request {
	static $instance;

	protected $uri;
	protected $baseUrl;
	protected $method;
	protected $rawData;

	public function __construct() {
		$this->baseUrl	= '/' . trim($_SERVER['SCRIPT_NAME'], ' /');
		$this->uri 		= '/' . trim(preg_replace('`^' . preg_quote(dirname($this->baseUrl)) .'(/'.preg_quote(basename($this->baseUrl)).')?`', '', $_SERVER['REQUEST_URI']), ' /');
		$this->method 	= strtoupper($_SERVER['REQUEST_METHOD']);
	}

	public function getMethod() {
		return $this->method;
	}

	public function getRawData() {
		if (null === $this->rawData && $this->method === 'PUT') 
			$this->rawData	= file_get_contents('php://input', FILE_BINARY);
		return $this->rawData;
	}

	public function getBaseUrl() {
		return $this->baseUrl;
	}

	public function __toString() {
		return $this->uri;
	}

	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}
