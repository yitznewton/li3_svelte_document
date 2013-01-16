<?php
namespace li3_mongo_svelte\extensions\adapter\data\source\MongoDb;

use lithium\core\Libraries;

class MongoSvelte extends \lithium\data\source\MongoDb
{
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
						$entity->{$relationKey}[$key] = $embeddedEntity;
					}
				}
			}
			var_dump($entity->facility_address_set);

			return $entity;
		};

		$this->applyFilter('read', function($self, $params, $chain) use ($processRelations) {
			$results = $chain->next($self, $params, $chain);
			$model = is_object($params['query']) ? $params['query']->model() : null;
			
			if (!$model || count($model::relations()) == 0) {
				return $results;
			}

			$results->applyFilter('_populate', function($self, $params, $chain)
				use ($model, $processRelations) {
				$item = $chain->next($self, $params, $chain);
				
				if ($item) {
					$item = $processRelations($item, $model::relations());
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

