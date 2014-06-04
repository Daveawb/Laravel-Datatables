<?php
namespace Daveawb\Datatables\Columns;

interface Expression {
    public function __construct(array $fields, $dataModel);
    public function evaluate($args = array());
	public function plain($args = array());
}
