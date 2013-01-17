<?php
namespace li3_svelte_document\extensions\data\collection;

class DocumentSet extends \lithium\data\collection\DocumentSet
{
	/**
	 * Lazy-loads a document from a query using a reference to a database adapter and a query
	 * result resource.
	 *
	 * Need to override this to make it filterable.
	 *
	 * @param array $data
	 * @param mixed $key
	 * @return array
	 */
	protected function _populate($data = null, $key = null) {
		$item = parent::_populate($data, $key);

		$params = compact('data', 'key', 'item');

		return $this->_filter(__METHOD__, $params, function($self, $params) {
			return $params['item'];
		});
	}
}

