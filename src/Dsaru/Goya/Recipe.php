<?php namespace Dsaru\Goya;

use Doctrine\DBAL\Schema\Schema;

abstract class Recipe 
{
	/**
	 * DBAL Schema instance
	 *
	 * @var \Doctrine\DBAL\Schema\Schema
	 */
	protected $schema;

	public function __construct(Schema $schema) 
	{
		$this->schema = $schema;
	}


}
