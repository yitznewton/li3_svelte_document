<?php
namespace li3_svelte_document\extensions\data;

class SvelteDocumentSchema extends \lithium\data\DocumentSchema
{
	public function _init()
	{
		parent::_init();
		$this->_classes['entity'] = 'li3_svelte_document\extensions\data\entity\Document';
	}
}

