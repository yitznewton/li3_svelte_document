<?php
namespace li3_mongo_svelte\extensions\data\entity;

class SvelteDocument extends \lithium\data\Entity
{
	public function set(array $data, array $options = array()) {
		$defaults = array('init' => false);
		$options += $defaults;

		foreach ($data as $key => $val) {
			unset($this->_increment[$key]);
			if (strpos($key, '.')) {
				$this->_setNested($key, $val);
				continue;
			}
			if ($cast) {
//				$pathKey = $this->_pathKey;
//				$model = $this->_model;
//				$parent = $this;
//				$val = $schema->cast($this, $key, $val, compact('pathKey', 'model', 'parent'));
			}
			$this->_updated[$key] = $val;
		}
	}
}

