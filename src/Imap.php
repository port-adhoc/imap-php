<?php
	namespace PortAdhoc\Imap;

	use PortAdhoc\Imap\Message;
	use Exception;

	class Imap {
		/**
		 * @var string 
		 */
		public $server;

		/**
		 * @var int
		 */
		public $port;

		/**
		 * @var array
		 */
		public $flags;

		/**
		 * @var string
		 */
		public $user;

		/**
		 * @var string
		 */
		public $password;

		/**
		 * @var string
		 */
		public $mailbox;

		/**
		 * @var ressource
		 */
		public $imap_handler;

		/**
		 * @var int
		 */
		public $connection_time;

		/**
		 * @var int
		 */
		public $message_fetching_time;

		/**
		 * @var string
		 */
		public $start;

		/**
		 * @var string
		 */
		public $end;

		public function __construct() {
			$this->server = '';
			$this->port = 993;
			$this->user = '';
			$this->password = '';
			$this->mailbox = 'INBOX';
			$this->imap_handler = null;
			$this->connection_time = 0;
			$this->message_fetching_time = 0;
			$this->start = '1';
			$this->end = '*';
		}

		public function __destruct() {}

		/**
		 * @throws Exception
		 */
		public function connect() {
			$begin = microtime(true);

			$this->imap_handler = imap_open( $this->getConnectionString(), $this->user, $this->password );

			$end = microtime(true);

			$this->connection_time = $end - $begin;

			if( $this->imap_handler === false ) {
				throw new Exception( imap_last_error() );
			}

			return $this;
		}

		/**
		 * @return string
		 */
		private function getFlagsAsString() {
			return ! empty($this->flags) ? '/' . implode('/', $this->flags) : '';
		}

		/**
		 * @return string
		 */
		private function getConnectionString() {
			return sprintf('{%s:%s%s}/%s', $this->server, $this->port, $this->getFlagsAsString(), $this->mailbox);
		}

		/**
		 * @return array
		 */
		public function getMessages() {
			$begin = microtime(true);

			$uids = imap_search( $this->imap_handler, 'ALL', SE_UID );

			$this->end = $this->end == '*' ? ((2**32) - 1) : $this->end;

			$messages = [];

			foreach( $uids as $uid ) {
				if( $uid >= $this->start && $uid <= $this->end ) {
					$messages[] = new Message( $this->imap_handler, $uid );	
				}
			}

			$end = microtime(true);

			$this->message_fetching_time = $end - $begin;

			return $messages;
		}

		/**
		 * @param int $uid
		 * @return PortAdhoc\Imap\Message
		 */
		public function getMessage( $uid ) {
			return new Message( $this->imap_handler, $uid );
		}
	}
?>