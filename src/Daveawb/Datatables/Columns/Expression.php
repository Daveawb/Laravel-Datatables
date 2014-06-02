<?php
namespace Daveawb\Datatables\Columns;

interface Expression {
    public function __construct(array $fields, $dataModel);
    public function evaluate($args = array());
}
