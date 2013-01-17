<?php
namespace li3_svelte_document\extensions\adapter\data\source\MongoDb;

use lithium\core\Libraries;
use lithium\data\Entity;
use BadMethodCallException;

class MongoSvelte extends \lithium\data\source\MongoDb
{
	public static function _applyModelToEmbedded(Entity $entity, array $relations)
	{
		foreach ($relations as $relation) {
			$relation = $relation->data();
			$relationKey = $relation['embedded'];
			$relationModel = $relation['to'];

			if (isset($entity->$relationKey)) {
				$embeddedRaw = $entity->$relationKey;

				foreach ($embeddedRaw as $key => $embeddedData) {
					$embeddedEntity = $relationModel::create(
						$embeddedData->toArray());
					self::_applyModelToEmbedded(
						$embeddedEntity, $relationModel::relations());
					$entity->$relationKey->setItem($key, $embeddedEntity);
				}
			}
		}

		return $entity;
	}

	public function _init()
	{
		parent::_init();
		$this->_classes['entity'] = 'li3_svelte_document\extensions\data\entity\Document';
		$this->_classes['schema'] = 'li3_svelte_document\extensions\data\SvelteDocumentSchema';
		$this->_classes['set'] = 'li3_svelte_document\extensions\data\collection\DocumentSet';
		$this->_classes['server'] = 'MongoClient';

		$this->applyFilter('read', function($self, $params, $chain) {
			$results = $chain->next($self, $params, $chain);

			if (is_object($params['query'])) {
				$model = $params['query']->model();
			}
			else {
				$model = null;
			}
			
			if (!$model || count($model::relations()) == 0) {
				return $results;
			}

			$results->applyFilter('_populate', function($self, $params, $chain)
				use ($model) {
				$item = $chain->next($self, $params, $chain);
				$relations = $model::relations();

				if (!$item) return $item;

				if ($relations) {
					$item = \li3_svelte_document\extensions\adapter\data\
						source\MongoDb\MongoSvelte::_applyModelToEmbedded(
							$item, $relations);
				}

				return $item;
			});

			return $results;
		});
	}

	/**
	 * Use fully-qualified namespace
	 */
	public function relationship($class, $type, $name, array $config = array()) {
		if (isset($config['to'])) {
			$config['to'] = Libraries::locate('models', $config['to']);
		}

		return parent::relationship($class, $type, $name, $config);
	}

	public function create($query, array $options = array())
	{
		throw new BadMethodCallException('li3_svelte_document is read-only');
	}

	public function update($query, array $options = array())
	{
		throw new BadMethodCallException('li3_svelte_document is read-only');
	}

	public function delete($query, array $options = array())
	{
		throw new BadMethodCallException('li3_svelte_document is read-only');
	}
}

