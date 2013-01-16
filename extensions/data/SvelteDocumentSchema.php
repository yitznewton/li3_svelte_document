<?php
namespace li3_mongo_svelte\extensions\data;

class SvelteDocumentSchema extends \lithium\data\DocumentSchema
{
	public function _init()
	{
		parent::_init();
		$this->_classes['entity'] = 'li3_mongo_svelte\extensions\data\entity\SvelteDocument';
	}
}

