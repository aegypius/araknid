<?php

class Araknid_Router {
	protected $request;
	protected $routes;
	protected $found = false;

	public function Araknid_Router() {
		$this->routes(array());
		$this->request('/'.trim($_SERVER['PATH_INFO'], '/'));
	}

	public function __get($property) {
		if ($p == 'request')
			return $this->request;
	}

	public function request($request) {
		$this->request = filter_var($request, FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		return $this;
	}

	public function routes($routes) {
		$this->routes = array();
		foreach ($routes  as $rule => $target) {
			$this->map($rule, $target);
		}
		return $this;
	}

	public function map($rule, $target = array(), $conditions = array()) {
		$this->routes[$rule] = new Araknid_Route($rule, $this->request, $target, $conditions);
		return $this;
	}

	public function execute() {
		foreach ($this->routes as $route) {
			if ($route->matched())
				return $this->route($route);
		}
	}

	protected function route($route) {
		$this->found = true;
		$params      = $route->params();

		$route       = new StdClass;
		$route->controller = $params['controller']; unset($params['controller']);
		$route->action     = $params['action']; unset($params['action']);
		$route->id         = $params['id'];
		$route->params     = array_merge($params, $_GET);

		return $route;
	}

// Static : -----------------------------------------------------------
	static $instance;
	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}
