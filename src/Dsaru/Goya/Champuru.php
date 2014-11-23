<?php namespace Dsaru\Goya;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;

class Champuru {


	/**
	 * The connection resolver instance.
	 *
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The DBAL Schema instance.
	 *
	 * @var \Doctrine\DBAL\Schema\Schema
	 */
	protected $schema;

	public function __construct( 
		Resolver $resolver,
		Filesystem $files
	)
	{
		$this->resolver = $resolver;
		$this->files    = $files;
		$this->schema   = new Schema();
	}

	/**
	 * Run
	 *
	 * @param  string  $path
	 * @param  bool    $cook
	 * @return void
	 */
	public function run($path, $cook=false)
	{
		$recipes = $this->getRecipeFiles($path);

		$this->requireFiles($path, $recipes);
		$this->readRecipeList($recipes);

		$queries = $this->createDiffSchema($this->schema);

		if ( $cook && !empty($queries) ) {
			$db = $this->getDBALConnection();
			foreach( $queries as $query ) {
				$db->executeUpdate($query);
			}
		}
		else {
			foreach( $queries as $query ) {
				echo $query, "\n";
			}
		}
	}

	/**
	 * Make schema diff from the current database.
	 *
	 * @return array
	 */
	public function createDiffSchema($schema)
	{
		$db = $this->getDBALConnection();
		$schemaManager = $db->getSchemaManager();

		$queries = $schema->getMigrateFromSql($schemaManager->createSchema(), $db->getDatabasePlatform());

		return $queries;
	}

	/**
	 * Get DBAL connection instance.
	 *
     * @return \Doctrine\DBAL\Connection
	 */
	public function getDBALConnection() {
		$connection = $this->resolver->connection();
		$db = DriverManager::getConnection(['pdo' => $connection->getPdo()]);

		return $db;
	}

	/**
	 * Get all of the recipe files in a given path.
	 *
	 * @param  string  $path
	 * @return array
	 */
	public function getRecipeFiles($path)
	{
		$files = $this->files->glob($path.'/*.php');

		if ($files === false) return array();

		$files = array_map(function($file)
		{
			return str_replace('.php', '', basename($file));

		}, $files);

		sort($files);

		return $files;
	}

	/**
	 * Require in all the recipe files in a given path.
	 *
	 * @param  string  $path
	 * @param  array   $files
	 * @return void
	 */
	public function requireFiles($path, array $files)
	{
		foreach ($files as $file) $this->files->requireOnce($path.'/'.$file.'.php');
	}

	/**
	 * Load table schema files.
	 *
	 * @param  array  $schemas
	 * @param  bool   $pretend
	 * @return void
	 */
	public function readRecipeList($recipes)
	{
		foreach ($recipes as $file)
		{
			$this->readRecipe($file);
		}
	}

	/**
	 * Load table schema file.
	 *
	 * @param  string  $file
	 */
	public function readRecipe($file)
	{
		$recipe = $this->resolve($file);
		$recipe->schema();
	}

	/**
	 * Resolve a recipe instance from a file.
	 *
	 * @param  string  $file
	 * @return object
	 */
	public function resolve($file)
	{
		$file = implode('_', array_slice(explode('_', $file), 1));

		$class = studly_case($file);

		return new $class($this->schema);
	}
}
