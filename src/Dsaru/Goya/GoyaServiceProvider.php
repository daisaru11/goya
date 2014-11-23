<?php namespace Dsaru\Goya;

use Illuminate\Support\ServiceProvider;

class GoyaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerChampuru();
		$this->registerCommands();
	}

	/**
	 * Register the champuru service.
	 *
	 * @return void
	 */
	protected function registerChampuru()
	{
		$this->app->bindShared('champuru', function($app)
		{
			return new Champuru($app['db'], $app['files']);
		});
	}

	/**
	 * Register commands
	 *
	 * @return void
	 */
	protected function registerCommands()
	{
		$this->registerGoyaCommand();
		$this->registerGoyaCookCommand();
		$this->commands( 'command.goya' );
		$this->commands( 'command.goya_cook' );
	}

	/**
	 * Register the "goya" command.
	 *
	 * @return void
	 */
	protected function registerGoyaCommand()
	{
		$this->app->bindShared('command.goya', function($app)
		{
			return new GoyaCommand($app['champuru']);
		});
	}

	/**
	 * Register the "goya:cook" command.
	 *
	 * @return void
	 */
	protected function registerGoyaCookCommand()
	{
		$this->app->bindShared('command.goya_cook', function($app)
		{
			return new GoyaCookCommand($app['champuru']);
		});
	}
}
