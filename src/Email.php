<?php
	namespace PortAdhoc\Imap;

	class Email {
		public $name;
		public $email;
		
		/**
		 * @param string $name
		 * @param string $email
		 */
		public function __construct( $name, $email ) {
			$this->name = $name;
			$this->email = $email;
		}
	}
?>