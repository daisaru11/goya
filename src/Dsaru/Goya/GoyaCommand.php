<?php namespace Dsaru\Goya;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GoyaCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'goya';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate Database Schema Diffs';

	/**
	 * The champuru instances
	 *
	 * @var \Goya\Champuru
	 */
	protected $champuru;

	/**
	 * Create a new command instance.
	 *
	 * @param \Goya\Champuru $champuru
	 * @return void
	 */
	public function __construct( Champuru $champuru )
	{
		parent::__construct();

		$this->champuru = $champuru;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$path = $this->getRecipePath();

		$this->champuru->run($path, false);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('path', null, InputOption::VALUE_OPTIONAL, 'A path to schema files.', null),
		);
	}

	/**
	 * Get the path to the recipe files directory.
	 *
	 * @return string
	 */
	protected function getRecipePath()
	{
		$path = $this->input->getOption('path');

		if ( ! is_null($path))
		{
			return $this->laravel['path.base'].'/'.$path;
		}


		return $this->laravel['path'].'/database/recipes';
	}

}
