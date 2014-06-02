<?php namespace Daveawb\Datatables;

use Illuminate\Support\ServiceProvider;

class DatatablesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('daveawb/datatables');
		
		// We bind the input class to use datatables 1.9.x hungarian notation as default
		$this->app->bind("Daveawb\Datatables\Columns\Input\BaseInput", "Daveawb\Datatables\Columns\Input\OneNineInput");
		// Bind the standard driver
		$this->app->bind("Daveawb\Datatables\Driver", "Daveawb\Datatables\Drivers\Laravel");
	}
	
	public function boot()
	{
		
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
