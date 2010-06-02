<?php

abstract class Araknid_Controller {
	public function Araknid_Controller() {}

	final static function view($view, $data=array()) {
		extract($data, EXTR_SKIP);
		include Araknid_Loader::getInstance()->view($view);
	}
}

