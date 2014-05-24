<?php
namespace Daveawb\Datatables
{
    use Exception; 

    class DatatablesException extends Exception {
    }

    class InputMissingException extends DatatablesException {
    }

    class ColumnCountException extends DatatablesException {
    }

}
