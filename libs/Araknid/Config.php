<?php

class Araknid_Config implements ArrayAccess, Countable, SeekableIterator {

	protected $data;
	protected $section;
	protected $current = 0;

	public function __construct($mixed, $section = null) {
	
		if (is_array($mixed))
			$data = $mixed;
		else if (defined('APP_CONFIG_PATH') && file_exists(APP_CONFIG_PATH . DIRECTORY_SEPARATOR . $mixed))
			$data = $this->loadFromFile(APP_CONFIG_PATH . DIRECTORY_SEPARATOR . $mixed);

		if (null !== $section && isset($data[$section]))
			$this->data = new self($data[$section]);
		else if (is_array($data))
			$this->data = $data;

	}

	public function & __get($var) {
		if (isset($this->data[$var]))
			return is_array($this->data[$var]) ? new self($this->data[$var]) : $this->data[$var];
	}

	public function __set($var, $value) {
		throw new Exception(__CLASS__ . ' is not writeable.');
	}

	public function toArray() {
		return $this->data;
	}

	protected function loadFromFile($filename) {
		
	}

	/*
		Function: offsetExists
			ArrayAccess::offsetExists implementation
		
		Parameters:
			$offset - offset to check

		Returns:
			whether the offset exists
	*/
	public function offsetExists($offset) {
		return (isset($this->data[$offset]));
	}

	/*
		Function: offsetGet
			ArrayAccess::offsetGet implementation
		
		Parameters:
			$offset - offset to retrieve

		Returns:
			value at given offset
	*/
	public function offsetGet($offset) {
		return ($this->offsetExists($offset) ? 
			(is_array($this->data[$offset]) ? new self($this->data[$offset]) : $this->data[$offset])
			: null);
	}

	/*
		Function: offsetSet
			ArrayAccess::offsetSet implementation
		
		Parameters:
			$offset - offset to modifiy
			$value  - new value

	*/
	public function offsetSet($offset, $value) {
		throw new Exception(__CLASS__ . ' is not writeable.');
	}
	
	/*
		Function: offsetUnset
			ArrayAccess::offsetUnset implementation
		
		Parameters:
			$offset - offset to delete

	*/
	public function offsetUnset($offset) {
		throw new Exception(__CLASS__ . ' is not writeable.');
	}
	
	/*
		Function: count
			Countable::count implementation
		
		Returns:
			the number the global function count() should show
	*/
	public function count() {
		return count($this->data);
	}
	
	/*
		Function: current
			SeekableIterator::current implementation
		
		Returns:
			the current element
	*/
	public function current() {
		$value = $this->data[$this->key($this->current)];
		return is_array($value) ? new self($value) : $value;
	}

	/*
		Function: key
			SeekableIterator::key implementation
		
		Returns:
			the key of the current element
	*/
	public function key() {
		$keys = array_keys($this->data);
		return $keys[$this->current];
	}

	/*
		Function: next
			SeekableIterator::next implementation
	*/
	public function next() {
		$this->current++;
	}

	/*
		Function: rewind
			SeekableIterator::rewind implementation
	*/
	public function rewind() {
		$this->current = 0;
	}

	/*
		Function: valid
			SeekableIterator::valid implementation
	*/
	public function valid() {
		return ($this->current < count($this->data));
	}

	/*
		Function: seek
			SeekableIterator::seek implementation
	*/
	public function seek($index) {
		$this->rewind();
		$position = 0;

		while ($position < $index && $this->valid()) {
			$this->next();
			$position++;
		}

		if (!$this->valid())
			throw new OutOfBoundException('Invalid seek position');
	}
	
}

