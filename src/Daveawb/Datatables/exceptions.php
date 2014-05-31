<?php
namespace Daveawb\Datatables
{
    use Exception;
	
	use Illuminate\Validation\Validator;

    class DatatablesException extends Exception {
    }

    class InputMissingException extends DatatablesException {
    }

    class ColumnCountException extends DatatablesException {
    }
	
	class ValidationException extends DatatablesException {
		public function __construct(Validator $validator)
		{
			$this->validator = $validator;
			$this->messages = $validator->errors();
		}
	}

}
