<?php
namespace li3_mongo_svelte\extensions\data\entity;

use li3_mongo_svelte\extensions\data\SvelteSet;

class Document extends \lithium\data\Entity
{
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
	}
}

