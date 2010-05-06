<?php

class Araknid_Router {
	static $instance;
	static $candidates = false;
	protected $routes = array(
		':module/:controller/:action',
		':module/:controller',
		':module',
		':controller/:action',
		':controller'
	);
	protected $defaults = array(
		'module' => null,
		'controller' => 'main',
		'action' => 'index'
	);

	public function __construct() {
		$request = Araknid_Request::getInstance();
		debug('Initializing '.__CLASS__);
		$this->initController($this->route($request));
	}

	protected function route($request) {
		debug(__CLASS__ . ' > Routing : ' . $request);
		$path = join(DIRECTORY_SEPARATOR, array(APP_PATH, 'controllers'));

		if (self::$candidates === false) {
			$items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($items as $item) {
				if ($item->isFile() && strpos(strtolower($item->getFilename()), PHP_EXT) !== false)
					self::$candidates[] = $item;
			}
		}
		$items = self::$candidates;

		foreach ($this->routes as $route) {
			$segs = explode('/', $route);
			foreach ($segs as &$seg) {
				switch ($seg) {
					case ':module'     : $seg = '(?<module>[^\/]+)';     break;
					case ':controller' : $seg = '(?<controller>[^\/]+)'; break;
					case ':action'     : $seg = '(?<action>[^\/]+)';     break;
				}
			}
			$route = '/^\/' . implode('\/', $segs). '/';

			debug(__CLASS__ . ' > Route : ' . $route);
			if (preg_match($route, strtolower($request), $m)) {
				// cleaning matches ugly way
				for ($i = 0; $i < count($m); $i++) unset($m[$i]);
				$m = array_merge($this->defaults, $m);

				list($module, $controller, $action) = array_values($m);

				$classname = ucfirst(strtolower($controller)) . 'Controller';
				$filename = $path . DIRECTORY_SEPARATOR . trim(join(DIRECTORY_SEPARATOR, array($module, $controller)) . PHP_EXT, DIRECTORY_SEPARATOR);

				$found = false;
				foreach ($items as $item) {
					if (strtolower($item->getPathname()) == $filename) include_once $item->getPathname();
					try {
						$C = new ReflectionClass($classname);
						if (!$C->hasMethod($action)) continue;
						return array($classname, $action);
					} catch (ReflectionException $e) {}
				}
			}
		}
		return false;
	}

	protected function initController($callback) {
		if ($callback == false) {
			throw new Araknid_Exception('Not Found', 404);
		}
		list($class, $method) = $callback;
		$R = new ReflectionClass($class);
		$C = $R->newInstance();
		$C->$method();
	}

	static function getInstance() {
		if (null === self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
}
