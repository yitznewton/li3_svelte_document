<?php
namespace li3_svelte_document\extensions\data;

use InvalidArgumentException;
use li3_svelte_document\extensions\data\entity\Document;

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

	public function __isset($name)
	{
		return isset($this->_data[$name]);
	}

	public function __set($name, $value)
	{
		if (is_array($value)) {
			$wrap = array(&$value);
			Document::castArraysToObject($wrap);
		}
		$this->_data[$name] = $value;
	}

	public function setItem($key, $value)
	{
		$this->_data[$key] = $value;
	}

	/**
	 * This is a method from \lithium\util\Collection that MDX used before
	 * implementing the new Svelte classes, so we are bringing it in
	 *
	 * @return mixed
	 */
	public function first()
	{
		return isset($this->_data[0]) ? $this->_data[0] : null;
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
		if (!is_scalar($offset)) {
			throw new InvalidArgumentException('offset must be scalar');
		}

		return array_key_exists($offset, $this->_data);
	}

	public function offsetGet($offset)
	{
		if (!is_scalar($offset)) {
			throw new InvalidArgumentException('offset must be scalar');
		}

		return $this->_data[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if (!is_scalar($offset)) {
			throw new InvalidArgumentException('offset must be scalar');
		}

		$this->_data[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if (!is_scalar($offset)) {
			throw new InvalidArgumentException('offset must be scalar');
		}

		if (!array_key_exists($offset, $this->_data)) return;

		unset($this->_data[$offset]);

		if (is_numeric($offset)) {
			// assume this is a numeric array and reset the array keys;
			// otherwise iteration will be messed up because of the gaps
			// left in the sequence of keys
			$this->_data = array_values($this->_data);
			if ($this->position > $offset) --$this->position;
		}
	}

	public function stats()
	{
		return isset($this->_data['stats']) ? $this->_data['stats'] : array();
	}

	public function toArray()
	{
		return $this->_data;
	}

	/**
	 * @see lithium\utils\Collection::map()
	 */
	public function map($filter, array $options = array()) {
		$defaults = array('collect' => true);
		$options += $defaults;
		$data = array_map($filter, $this->_data);

		if ($options['collect']) {
			$class = get_class($this);
			return new $class($data);
		}
		return $data;
	}

	public function to($type)
	{
		if ($type == 'array')
		{
			return $this->toArray();
		}

		throw new InvalidArgumentException('Only supports "array"');
	}
}

