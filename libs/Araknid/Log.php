<?php

class Araknid_Log {
	const LOG_LEVEL_ERROR   = LOG_ALERT;
	const LOG_LEVEL_WARNING = LOG_WARNING;
	const LOG_LEVEL_DEBUG   = LOG_DEBUG;
/* --------------------------------------------------------------------
  Protected:
-------------------------------------------------------------------- */
	protected $path;

	protected function Araknid_Log() {
		if (!openlog('AraknidLog', LOG_ODELAY, LOG_USER))
			throw new Exeception('syslog is not available on this system');
		self::debug('Initilializing '.__CLASS__);
	}

	protected function write($level, $message) {
		syslog($level, $message);
	}

/* --------------------------------------------------------------------
  Public:
-------------------------------------------------------------------- */
	public function __call($method, $args) {
		switch($method) {
			default        :
			case 'debug'   : $level = self::LOG_LEVEL_DEBUG; break;
			case 'warning' : $level = self::LOG_LEVEL_WARNING; break;
			case 'error'   : $level = self::LOG_LEVEL_ERROR; break;
		}

		if (count($args) == 0) {
			throw new Exception(sprintf('%s::%s() expect at least one argument.', __CLASS__, $method));
		} else if (count($args) == 1) {
			$message = implode('',$args);
		} else {
			if (strpos($args[0],'%') === false) {
				$message = implode(' ', $args);
			} else {
				$proto = array_shift($args);
				$message = vsprintf($proto, $args);
			}
		}
		$this->write($level, $message);
	}

	public function log($level, $message) {
		$this->write($level, $message);
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