<?php
	class AjaxResponse{
		public $responseCode; // 0 all ok - 1 some errors - -1 some warning
		public $message;
		public $data;
		
		function AjaxResponse($responseCode = 1, 
								$message = "Somenthing went wrong! Please try later.",
								$data = null){
			$this->responseCode = $responseCode;
			$this->message = $message;
			$this->data = $data;
		}
	
	}
?>