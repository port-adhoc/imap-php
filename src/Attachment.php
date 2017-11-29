<?php
	namespace PortAdhoc\Imap;

	use PortAdhoc\Imap\StringDecoder;
	use PortAdhoc\Imap\Encoding;

	class Attachment {
		/**
		 * @var resource
		 */
		protected $imap_handler;
		
		/**
		 * @var int
		 */
		protected $uid;

		/** 
		 * @var string
		 */
		protected $section;

		/**
		 * @var string
		 */
		protected $file_name;

		/**
		 * @var string
		 */
		protected $encoding;

		/**
		 * @var bool
		 */ 
		protected $body_fetched;

		/**
		 * @var string
		 */
		protected $body;

		/**
		 * @param resource $imap_handler
		 * @param int $uid
		 * @param string $section
		 * @param int $encoding
		 * @param string $name
		 */
		public function __construct( $imap_handler, $uid, $section, $encoding, $file_name ) {
			$this->imap_handler = $imap_handler;
			$this->uid = $uid;
			$this->section = $section;
			$this->encoding = $encoding;
			$this->file_name = $file_name;
			$this->body_fetched = false;
			$this->body = '';
		}

		public function getFileName() {
			return $this->file_name;
		}

		/**
		 * @return string
		 */
		public function getContent( $encoding = Encoding::UTF_8 ) {
			$this->getBody( $encoding );

			return $this->body;
		}

		private function getBody( $encoding ) {
			if( ! $this->body_fetched ) {
				$body = imap_fetchbody( $this->imap_handler , $this->uid, $this->section, FT_UID & FT_PEEK);

				$this->body = StringDecoder::getDecodedString( $body, $this->encoding, $encoding );
			}
		}
	}
?>