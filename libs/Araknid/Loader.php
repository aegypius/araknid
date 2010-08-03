<?php

class Araknid_Loader {

   // Protected : ------------------------------------------------------
   protected $callbacks = array();

   protected function __construct() {
      // Register default callback
      $this->callbacks[] = array(&$this, 'pear_loader');
   }

   protected function pear_loader($classname) {
      // Retrieving include path
      $path     = explode(PATH_SEPARATOR, dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

      // Convert path to absolute paths
      foreach ($path as &$p)
         $p    = realpath($p);

      // Convert classname to path
      $ext      = (defined('PHP_EXT') ? PHP_EXT : pathinfo(__FILE__, PATHINFO_EXTENSION));
      $filepath = implode(DIRECTORY_SEPARATOR, explode('_', $classname)) . $ext;

      // Let's search class file without case-sensitivness
      foreach ($path as $p) {
         $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($p), RecursiveIteratorIterator::SELF_FIRST);
         foreach ($items as $item) {
            if ($item->isFile() && (strtolower($p . DIRECTORY_SEPARATOR . $filepath) == strtolower($item->getRealpath()))) {
               include $item->getRealpath();
               if (class_exists($classname))
                  return;
            }
         }
      }
   }

   // Public : ------------------------------------------------------
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


   // Static : ---------------------------------------------------------
   static function & register($callback) {
      $callbacks =& self::getInstance()->callbacks;
      if (!is_callable($callback))
         throw new Exception('Argument is not a valid callback.');
      if (!in_array($callback, $callbacks))
         $callbacks[] = $callback;
      return self::getInstance();
   }

   static function loader($class) {
      reset(self::getInstance()->callbacks);
      while (!class_exists($class) && (list(,$callback) = each(self::getInstance()->callbacks))) {
          call_user_func_array($callback, array($class));
      }
   }

   static $instance;
   static function & getInstance() {
      if (!isset(self::$instance))
         self::$instance = new self();
      return self::$instance;
   }

}