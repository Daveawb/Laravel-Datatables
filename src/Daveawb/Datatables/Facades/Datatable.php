<?php namespace Daveawb\Datatables\Facades;

use Illuminate\Support\Facades\Facade;

class Datatable extends Facade {
	
	protected static function getFacadeAccessor() 
	{
		return 'Daveawb\Datatables\DatatableInterface';
	}
	
}