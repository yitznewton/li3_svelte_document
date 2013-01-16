<?php
namespace li3_mongo_svelte\extensions\data;

class SvelteSet implements \Iterator, \ArrayAccess, \Countable
{
	private $_data = array();
	private $position = 0;

	public function __construct(array $data)
	{
		$this->_data = $data;
	}

	public function __get($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : null;
	}

	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}

	public function setItem($key, $value)
	{
		$this->_data[$key] = $value;
	}

	public function first()
	{
		return $this->_data[0];
	}

	public function current()
	{
		return $this->_data[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function rewind()
	{
		$this->position = 0;
	}

	public function valid()
	{
		return isset($this->_data[$this->position]);
	}

	public function count()
	{
		return count($this->_data);
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->_data);
	}

	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	public function toArray()
	{
		return $this->_data;
	}
}

