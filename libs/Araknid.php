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
		// Register Exception Handlers

		set_error_handler(array(__CLASS__, "errorHandler"));
		set_exception_handler(array(__CLASS__, "exceptionHandler"));

		//$this->request = Araknid_Request::getInstance();
		$this->router  = Araknid_Router::getInstance();

		// Setup default routes
		$this->router->routes(array(
			'/'                    => array('controller' => 'main', 'action' => 'index'),
			'/:controller'         => array('action' => 'index'),
			'/:controller/:action' => array()
		));

		if (!($route = $this->router->execute())) {
			throw new Exception('No Route for ' . $this->router->request);
		}

		// Initializing controller
		if (is_callable(array($route->controller, $route->action)) && class_exists($route->controller)) {
			$R = new ReflectionClass($route->controller);
			$O = $R->newInstance($route->params['id']);
			$O->params = $route->params;
			$method = $route->action;
			$response = $O->$method();
			// $response = call_user_func_array(array(&$O, $route->action),  $route->params);
			return;
		} else if (!class_exists($route->controller)) {
			throw new Exception("Not A Valid Controller: '{$route->controller}'");
		}
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

	static function errorHandler($errno, $errstr, $errfile, $errline) {
		$enabled = (bool)($errno & ini_get('error_reporting'));

		if (in_array($errno, array(E_USER_ERROR, E_RECOVERABLE_ERROR)) && $enabled ) {
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		} else if ($enabled) {
			$errors = array(
				1     => 'E_ERROR',
				2     => 'E_WARNING',
				4     => 'E_PARSE',
				8     => 'E_NOTICE',
				16    => 'E_CORE_ERROR',
				32    => 'E_CORE_WARNING',
				64    => 'E_COMPILE_ERROR',
				128   => 'E_COMPILE_WARNING',
				256   => 'E_USER_ERROR',
				512   => 'E_USER_WARNING',
				1024  => 'E_USER_NOTICE',
				2048  => 'E_STRICT',
				4096  => 'E_RECOVERABLE_ERROR',
				8192  => 'E_DEPRECATED',
				16384 => 'E_USER_DEPRECATED',
			);
			debug($errors[$errno], ':', $errstr, $errfile. ':'. $errline);
		}

	}

	static function exceptionHandler($exception) {
		$classname = get_class($exception);
		error($classname, ':', $exception->getCode(), '-', $exception->getMessage(), Araknid_Request::getInstance());
		switch ($classname) {
			case 'Araknid_Exception' :
				$header = sprintf('%d - %s', $exception->getCode(), $exception->getMessage());
				header('HTTP/1.1 ' . $header);
				print "<h1>$header</h1>";
				switch ($exception->getCode()) {
					case 404: $msg = "<p>I did'nt find what you've requested.</p><q>I think you ought to know I'm feeling very depressed.</q>"; break;
					case 500: $msg = "<p>Something just happened and it's barely bad !</p><q>Incredible... it's even worse than I thought it would be.</q>"; break;
				}
				print $msg;
				return;

			case 'ErrorException' :
		}
		printf(
			'<h1>%s</h1>'  .
			'<pre>%s</pre>',
			$classname,
			$exception
		);
	}
}

/* Autoloader ------------------------------------------------------ */
if ((!defined('ARAKNID_AUTOLOAD')) || (ARAKNID_AUTOLOAD == true)) {
	include_once dirname(__FILE__).'/Araknid/Loader.php';
	if (function_exists('spl_autoload_register')) {
		spl_autoload_register(array('Araknid_Loader', 'loader'));
	} else {
		function __autoload($classname) {
			Araknid_Loader::loader($classname);
		}
	}

	/* Initializing Log System ------------------------------------- */
	Araknid_Log::getInstance();
}


