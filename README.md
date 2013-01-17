# [Lithium PHP](http://lithify.me) Plugin to allow embedded relations and slim subdocument sets for MongoDB

**Warning**: This only supports READ operations. 

A plugin to add support to li3 embedded relations for MongoDb. Lithiums mongo adapter says it supports embedded relations, but after much investigation through the core it appears this is not the case. Basically, when an embedded relation is specified, it does the single query for the parent and when the data is returned it creates the appropriate model with the data returned from the parent.

Secondly, the DocumentSet class which represents the array at each level
of a Mongo document, has metadata attached. In applications with heavily
branched documents, that metadata can add tens of MB of RAM overhead.
This substitutes a simple Iterator/ArrayAccess wrapper for the array of
data.

DocumentSet is still used for the main container set of documents.

## Installation

1. Clone/Download/submodule the plugin into your app's ``libraries`` directory.
2. Tell your app to load the plugin by adding the following to your app's ``config/bootstrap/libraries.php``:

## Usage

Add the plugin in your `config/bootstrap/libraries.php` file:

~~~ php
<?php
	Libraries::add('li3_svelte_document');
?>
~~~

Next, in your app/config/connections.php specify this extended MongoDB adapter.
~~~ php
Connections::add('default', array(
	'type' => 'MongoDb', 
	'adapter' => 'MongoSvelte', 
	'host' => 'localhost', 
	'database' => 'foo'
));
~~~

#### Using an Embedded Relation

Continue defining relations in the lithium specified way as described [here](http://lithify.me/docs/manual/working-with-data/relationships.wiki), except for the embedded key

~~~ php
class Team extends  \lithium\data\Model.php {

	public $hasMany = array(
		'Players' => array(
			'to' 	   => 'Players',
			'embedded' => 'players'
 		),
 		'Scouts' => array(
 			'to' => 'Scouts',
 			'embedded' => 'scouts',
 		),
	);

~~~

Key specified is the name used to reference the relation on a find query.

Options are:  

* to - specified target model  
* embedded  - the key on which the data is embedded  


## Some Notes

* Beta Beta Beta - Currently, this plugin is being used heavily in a read
  MongoDB environment, but that's it. The SvelteSet substitute for
  DocumentSet in nested subdocuments also does not support all the
  `lithium\util\Collection` operations that DocumentSet does.

