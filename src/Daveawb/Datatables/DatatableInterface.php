<?php namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;
use Daveawb\Datatables\Input;

use Illuminate\Database\Query\Builder;

interface DatatableInterface {
	public function __construct(Input $input, Factory $factory);
	public function model($model);
    public function query(Builder $builder);
}
