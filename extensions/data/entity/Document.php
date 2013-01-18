<?php
namespace li3_svelte_document\extensions\data\entity;

use li3_svelte_document\extensions\data\SvelteSet;

class Document extends \lithium\data\Entity
{
	public function __set($name, $value = null)
	{
		parent::__set($name, $value);
		if (is_array($value)) {
			$wrap = array(&$this->_updated[$name]);
			self::castArraysToObject($wrap);
		}
	}

	public function _init()
	{
		parent::_init();
		self::castArraysToObject($this->_updated);
	}

	private static function castArraysToObject(array &$data)
	{
		foreach ($data as &$item) {
			if (is_array($item)) {
				self::castArraysToObject($item);
				$item = new SvelteSet($item);
			}
		}

		// CAUTION: $item is set by reference above; unsetting so nobody will
		// overwrite it
		unset($item);
	}
}

