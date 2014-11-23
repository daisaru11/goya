# Goya #

Goyaは[Laravel](http://laravel.com/)のためのシンプルなデータベーススキーマ管理ツールです。
スキーマ定義ファイルとデータベースとの差分を出力し、適用することができます。

スキーマの差分は[Doctrine DBAL](http://www.doctrine-project.org/projects/dbal.html)を利用して出力しています。
スキーマ定義ファイルはDBALのスキーマ表現を用いて記述します。

## Using Goya ##

スキーマ定義ファイル(Recipeと呼んでいます)を作成します。
スキーマの記述方法はDBALのドキュメントを参照してください。

	$ cat app/database/recipes/UserTable.php                                                                                                                               	<?php
	
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
	
まだ、実際のデータベースには定義は存在していません。

	$ mysql -uroot yourdb 
	mysql> show tables;
	Empty set (0.00 sec)
	
`goya`コマンドを実行すると、スキーマ定義ファイルを元にSQLを出力します。

	$ php artisan goya                                                                                                                                                          	CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB

`goya:cook`コマンドで実際にデータベースへ適用します。

	$ php artisan goya                                            

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
	

スキーマ定義ファイルを修正してみます。`profile`カラムを追加してみます。


	$table->addColumn( 'profile',        'string',   ['length' => 1000] );

再度、`goya`コマンドを実行します。

	$ php artisan goya                                                                                                                                                          
	ALTER TABLE user ADD profile VARCHAR(1000) NOT NULL
	
スキーマ定義ファイルとデータベース上のスキーマとの差分を得ることができます。

## Installation ##

## Writing Recipes ##

スキーマ定義ファイル(Recipe)は、`app/database/recipes`以下に置いてください。  
複数のテーブル定義を、一つのファイルに記述しても良いし、複数のファイルに分割をしても良いです。

`app/database/recipes`以下に`[クラス名].php`ファイルを作成してください。  
その中に`\Dsaru\Goya\Recipe`を継承したクラスを定義し、`schema`メソッドの中にデータベースのスキーマ定義を記述していきます。


	class MyTables extends \Dsaru\Goya\Recipe
	{
		public function schema() {
			// write database definitions here
		}
	}
	

スキーマ定義ファイルを分割した際、読み込まれる順序をコントロールしたい場合があります。
スキーマ定義ファイルはアルファベット順で読み込まれるので、例えば`001_[クラス名].php`、`002_[クラス名].php`とすると良いです。

## Author ##

daisaru11 <<daisaru11@gmail.com>>