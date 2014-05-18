<?php namespace Daveawb\Datatables;

use Daveawb\Datatables\Support\Input;
use Illuminate\Database\Eloquent\Model;

interface DatatableInterface {
	public function __construct(Input $input);
	public function model($model = null);
}
