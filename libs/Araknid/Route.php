<?php

class Araknid_Route {
	protected $matched = false;
	protected $params;
	protected $rule;
	protected $conditions;

	public function Araknid_Route($rule, $request, $target, $conditions=array()) {
		$this->rule = $rule;
		$this->params = array();
		$this->conditions = $conditions;
		$p_names = array(); $p_values = array();

		preg_match_all('@:([\w]+)@', $rule, $p_names, PREG_PATTERN_ORDER);
		$p_names = $p_names[0];

		$url_regex = preg_replace_callback('@:[\w]+@', array($this, 'regex'), $rule);
		$url_regex .= '/?';

		if (preg_match('@^' . $url_regex . '$@', $request, $p_values)) {
			array_shift($p_values);
			foreach($p_names as $index => $value) $this->params[substr($value,1)] = urldecode($p_values[$index]);
			foreach($target as $key => $value) $this->params[$key] = $value;
			$this->matched = true;
		}

		unset($p_names); unset($p_values);

	}

	public function matched() {
		return $this->matched;
	}

	public function params() {
		return $this->params;
	}

	protected function regex($matches) {
		$key = str_replace(':', '', $matches[0]);
		if (array_key_exists($key, $this->conditions)) {
			return '('.$this->conditions[$key].')';
		}
		else {
			return '([a-zA-Z0-9_\+\-%]+)';
		}
	}

}