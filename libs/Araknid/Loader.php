<?php

class Araknid_Loader {

	public function controller($class, $data = array()) {
	}

	public function view($view) {
		$paths = explode(PATH_SEPARATOR, APP_VIEWS_PATH);
		foreach ($paths as $path) {
			$f = join('/', array($path, $view));
			if (!strpos(PHP_EXT, $view)) $f .= PHP_EXT;
			if (file_exists($f))
				return $f;
		}
		throw new Exception("View Not Found : $view");
	}

	static function loader($class) {
		// Retrieving include path
		$path     = explode(PATH_SEPARATOR, dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

		// Convert path to absolute paths
		foreach ($path as &$p)
			$p    = realpath($p);

		// Convert classname to path
		$ext      = (defined('PHP_EXT') ? PHP_EXT : pathinfo(__FILE__, PATHINFO_EXTENSION));
		$filepath = implode(DIRECTORY_SEPARATOR, explode('_', $class)) . $ext;

		// Let's search class file without case-sensitivness
		foreach ($path as $p) {
			$items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($p), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($items as $item) {
				if ($item->isFile() && (strtolower($p . DIRECTORY_SEPARATOR . $filepath) == strtolower($item->getRealpath()))) {
					include $item->getRealpath();
					if (class_exists($class))
						return;
				}
			}
		}
	}

	static $instance;
	static function getInstance() {
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

}