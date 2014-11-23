# Goya #

Goya is a simple database schema management tool for [Laravel](http://laravel.com/).
Goya output diffs between schema definition files and database schemas, apply them to the database.

To make diff of a schema, Goya uses [Doctrine DBAL](http://www.doctrine-project.org/projects/dbal.html).
Schema definition files are described in DBAL Schema Representation.


## Using Goya ##

Create schema definition files (Recipes).  
For how to describe schemas, see DBAL Docmentations.

	$ cat app/database/recipes/UserTable.php
	<?php
	
	use Dsaru\Goya\Recipe;
	use Doctrine\DBAL\Schema\Schema;
	
	class UserTable extends Recipe
	{
	
		public function schema() {
			$table = $this->schema->createTable('user');
	
			$table->addColumn( 'id',             'integer',  ['autoincrement' => true, 'unsigned' => true] );
			$table->addColumn( 'name',           'string',   ['length' => 32] );
			$table->addColumn( 'created_at',     'datetime', [] );
			$table->addColumn( 'updated_at',     'datetime', [] );
			$table->addColumn( 'deleted_at',     'datetime', ['notnull' => false, 'default' => null] );
	
			$table->setPrimaryKey(['id']);
		}
	
	}
	
There has been no definitions yet in the actual database.

	$ mysql -uroot yourdb 
	mysql> show tables;
	Empty set (0.00 sec)
	
If you run `goya` command, a SQL is generated from the schema definition files.

	$ php artisan goya
	CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB

you may apply it to the actual database with `goya:cook` command.

	$ php artisan goya:cook

	$ mysql -uroot yourdb 
	mysql> desc user;
	+------------+------------------+------+-----+---------+----------------+
	| Field      | Type             | Null | Key | Default | Extra          |
	+------------+------------------+------+-----+---------+----------------+
	| id         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
	| name       | varchar(32)      | NO   |     | NULL    |                |
	| created_at | datetime         | NO   |     | NULL    |                |
	| updated_at | datetime         | NO   |     | NULL    |                |
	| deleted_at | datetime         | YES  |     | NULL    |                |
	+------------+------------------+------+-----+---------+----------------+
	5 rows in set (0.00 sec)
	

Now, add `profile` column definition to the schema definition file.

	$table->addColumn( 'profile',        'string',   ['length' => 1000] );

Run `goya` command again.

	$ php artisan goya
	ALTER TABLE user ADD profile VARCHAR(1000) NOT NULL

	
## Installation ##

Add this package name to `composer.json`.

    "require": {
        ...,
        "dsaru/goya": "dev-master"
    }
    
Run `composer update`.

Open `app/config/app.php`, and add the Service Provider.  
	
	'providers' => array(
	 	..
		'Dsaru\Goya\GoyaServiceProvider', 
	),


## Writing Recipes ##

Schema definition files (Recipes) are put in `app/database/recipes`.  
You may describe multiple table definitions in a single file.
Or, it may be split into multiple files.

Recipe files are need to be named like `[ClassName].php`.
Create a class which extends `\Dsaru\Goya\Recipe` in the file, and describe database table schemas in `schema` method.


	class MyTables extends \Dsaru\Goya\Recipe
	{
		public function schema() {
			// write database definitions here
		}
	}
	
When you use multiple recipe files, you may need to control the order of the loading files.  
Because recipes are loaded in alphabetical order, it will work well if they are named like `001_[ClassName]].php`、`002_[ClassName].php`.

## Author ##

daisaru11 <<daisaru11@gmail.com>>
