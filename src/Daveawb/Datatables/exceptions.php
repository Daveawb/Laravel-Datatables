<?php
namespace Daveawb\Datatables
{
    use Exception;
	
	use Illuminate\Validation\Validator;

    class DatatablesException extends Exception {
    }

    class InputMissingException extends DatatablesException {
    }
	
	class ValidationException extends DatatablesException {
			
		protected $messages;
		protected $validator;
	
		/**
		 * We are adjusting this constructor to receive an instance
		 * of the validator as opposed to a string to save us some typing
		 * @param Validator $validator failed validator object
		 */
		public function __construct($validator)
	    {
			$this->validator = $validator;
			$this->messages = $validator->messages();
			parent::__construct($this->messages, 400);
		}
	
		public function getMessages()
		{
			return $this->messages->toArray();
		}
		
		public function getValidator()
		{
			return $this->validator;
		}
	};

}
