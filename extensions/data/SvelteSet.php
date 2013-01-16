<?php
namespace li3_mongo_svelte\extensions\data;

class SvelteSet
{
	private $_data = array();

	public function __construct(array $data)
	{
		$this->_data = $data;
	}

	public function __get($name)
	{
		return isset($this->_data[$name]) ? $data[$name] : null;
	}

	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	}
}

