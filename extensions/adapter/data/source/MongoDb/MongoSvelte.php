<?php
namespace li3_mongo_svelte\extensions\adapter\data\source\MongoDb;

use lithium\core\Libraries;
use lithium\data\Entity;

class MongoSvelte extends \lithium\data\source\MongoDb
{
	public static function _applyModelToEmbedded(Entity $entity, array $relations)
	{
		foreach ($relations as $relation) {
			$relation = $relation->data();
			$relationKey = $relation['embedded'];
			$relationModel = $relation['to'];

			if (isset($entity->$relationKey)) {
				$embeddedSet = $entity->$relationKey;

				foreach ($embeddedSet as $key => $embeddedArray) {
					$embeddedEntity = $relationModel::create($embeddedArray);
					self::_applyModelToEmbedded($embeddedEntity, $relationModel::relations());
					$entity->{$relationKey}[$key] = $embeddedEntity;
				}
			}
		}

		return $entity;
	}

	public function _init()
	{
		parent::_init();
		$this->_classes['entity'] = 'li3_mongo_svelte\extensions\data\entity\SvelteDocument';
		$this->_classes['schema'] = 'li3_mongo_svelte\extensions\data\SvelteDocumentSchema';
		$this->_classes['set'] = 'li3_mongo_svelte\extensions\data\collection\DocumentSet';

		$processRelations = function($entity, array $relations) {
			// FIXME this needs to be converted to a Collection?
			foreach ($relations as $relation) {
				$relation = $relation->data();
				$relationKey = $relation['embedded'];
				$relationModel = $relation['to'];

				if (isset($entity->$relationKey)) {
					$embeddedSet = $entity->$relationKey;

					foreach ($embeddedSet as $key => $embeddedArray) {
						$embeddedEntity = $relationModel::create($embeddedArray);
						var_dump($relationModel::relations());
						$entity->{$relationKey}[$key] = $embeddedEntity;
					}
				}
			}

			return $entity;
		};

		$this->applyFilter('read', function($self, $params, $chain) {
			$results = $chain->next($self, $params, $chain);
			$model = is_object($params['query']) ? $params['query']->model() : null;
			
			if (!$model || count($model::relations()) == 0) {
				return $results;
			}

			$results->applyFilter('_populate', function($self, $params, $chain)
				use ($model) {
				$item = $chain->next($self, $params, $chain);
				
				if ($item) {
					$item = \li3_mongo_svelte\extensions\adapter\data\source\MongoDb\MongoSvelte::_applyModelToEmbedded($item, $model::relations());
				}

				return $item;
			});

			return $results;
		});
	}

	/**
	 * Extended for full namesapce support
	 */
	public function relationship($class, $type, $name, array $config = array()) {
		if(isset($config['to'])){
			$config['to'] = Libraries::locate('models', $config['to']);
		}
		return parent::relationship($class, $type, $name, $config);
	}

}

