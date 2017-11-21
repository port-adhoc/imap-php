<?php
	namespace PortAdhoc\Imap;

	use InvalidArgumentException;
	use StdClass;
	use PortAdhoc\Imap\Email;
	use DateTime;
	use PortAdhoc\Imap\Attachment;
	use PortAdhoc\Imap\StringDecoder;

	class Message {
		const SECTION_BODY = '0';
		const SECTION_TEXT_PLAIN = '1.1';
		const SECTION_HTML = '1.2';

		const SUBTYPE_TEXT = 'PLAIN';
		const SUBTYPE_HTML = 'HTML';

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
		public $raw;

		/**
		 * @var int
		 */
		public $raw_fetching_time;

		/**
		 * @var int
		 */
		public $plain_text_fetching_time;

		/**
		 * @var int
		 */
		public $html_fetching_time;

		/**
		 * @var bool
		 */
		private $header_fetched;

		/**
		 * @var 
		 */
		private $header;

		/**
		 * @var bool
		 */
		private $structure_fetched;

		/**
		 * @var int
		 */
		private $header_section;

		/**
		 * @var array
		 */
		private $attachements_sections;

		/**
		 * @param resource $imap_handler
		 * @param int $uid
		 * @throws InvalidArgumentException
		 */
		public function __construct( $imap_handler, $uid ) {
			$resource_type = @get_resource_type($imap_handler);

			if( $resource_type === false || $resource_type !== 'imap' ) {
				throw new InvalidArgumentException(sprintf("parameter 1 must be an imap resource (%s given)", gettype($imap_handler)));
			}

			if( ! is_int( $uid ) ) {
				throw new InvalidArgumentException(sprintf('parameter must be an int (%s given)', gettype($uid)));
			}

			$this->imap_handler = $imap_handler;
			$this->uid = $uid;
			$this->raw = '';
			$this->text_plain_fetching_time = 0;
			$this->html_fetching_time = 0;
			$this->plain_text = '';
			$this->header_fetched = false;
			$this->header = new StdClass();
			$this->structure_fetched = false;
			$this->structure = '';
			$this->html_section = 2;
			$this->html_transfer_encoding = ENCOTHER;
			$this->plain_text_section = 1;
			$this->plain_text_transfer_encoding = ENCOTHER;
			$this->header_section = 0;
			$this->attachements_sections = [];
		}

		public function getImap() {
			return $this->imap_handler;
		}

		/**
		 * @return Khalyomede\Imap\Message;
		 */
		public function getRaw() {
			$begin = microtime(true);

			$this->raw = imap_body( $this->imap_handler, $this->uid, FT_UID & FT_PEEK );

			$this->raw_fetching_time = microtime(true) - $begin;

			return $this;
		}

		public function getHeader() {
			$this->getHeaderInfo();

			return $this->header;
		}

		/**
		 * @return string
		 */
		public function getPlainText() {
			$begin = microtime(true);

			$this->getStruct();

			$plain_text = imap_fetchbody( $this->imap_handler, $this->uid, $this->plain_text_section, FT_UID & FT_PEEK );

			$plain_text = StringDecoder::getDecodedString( $plain_text, $this->plain_text_transfer_encoding );

			$this->plain_text_fetching_time = microtime(true) - $begin;

			return $plain_text;
		}

		/**
		 * @return int
		 */
		public function getUid() {
			return $this->uid;
		}

		/**
		 * @return PortAdhoc\Imap\Email
		 */
		public function getFrom() {
			$this->getHeaderInfo();

			$name = $this->header->from[0]->personal;
			$email = $this->header->from[0]->mailbox . '@' . $this->header->from[0]->host;

			return new Email( $name, $email );
		}

		/**
		 * @return array
		 */
		public function getTo() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->to as $to ) {
				$name = $to->personal;
				$email = $to->mailbox . '@' . $to->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return string
		 */
		public function getHtml() {
			$begin = microtime(true);

			$this->getStruct();

			$html = imap_fetchbody( $this->imap_handler, $this->uid, $this->html_section, FT_UID & FT_PEEK );

			$html = StringDecoder::getDecodedString( $html, $this->html_transfer_encoding );

			$this->html_fetching_time = microtime(true) - $begin;

			return $html;
		}

		private function getStruct() {
			if( ! $this->structure_fetched ) {
				$this->structure = imap_fetchstructure($this->imap_handler, $this->uid, FT_UID & FT_PEEK);

				if( $this->structure->type == TYPEMULTIPART) {
					$this->header_section = 0;
				}

				if( property_exists( $this->structure, 'parts' ) ) {
					foreach( $this->structure->parts as $index => $part ) {
						if( property_exists( $part, 'parts' ) ) {
							if( property_exists( $this->structure, 'parts' ) ) {
								foreach( $part->parts as $index => $sub_part ) {
									if( $sub_part->type == TYPETEXT && strtoupper($sub_part->subtype) == self::SUBTYPE_HTML ) {
										$this->html_transfer_encoding = $sub_part->encoding;
										$this->html_section = $index + 1;
									}
									else if( $sub_part->type == TYPETEXT && strtoupper($sub_part->subtype) == self::SUBTYPE_TEXT ) {
										$this->plain_text_transfer_encoding = $sub_part->encoding;
										$this->plain_text_section = $index + 1;
									}
									
									if( $sub_part->ifdisposition && strtolower($sub_part->disposition) == 'attachment' && in_array($sub_part->type, [TYPETEXT, TYPEAPPLICATION, TYPEAUDIO, TYPEIMAGE, TYPEVIDEO]) ) {
										$attachement_section = [
											'section' => $index + 1,
											'encoding' => $sub_part->encoding,
											'filename' => $this->uid . '-' . (new DateTime())->getTimestamp() . 'txt'
										];

										if( $sub_part->ifdparameters ) {
											foreach( $sub_part->dparameters as $dparameter ) {
												if( strtolower($dparameter->attribute) == 'filename' ) {
													$attachement_section['filename'] = $dparameter->value;
												}
											}
										}
										else if( $sub_part->ifdescription ) {
											$attachement_section['filename'] = $sub_part->description;
										}

										$this->attachements_sections[] = $attachement_section;
									}
								}					
							}
						}
						else {
							if( $part->type == TYPETEXT && strtoupper($part->subtype) == self::SUBTYPE_HTML ) {
								$this->html_transfer_encoding = $part->encoding;
								$this->html_section = $index + 1;
							}
							else if( $part->type == TYPETEXT && strtoupper($part->subtype) == self::SUBTYPE_TEXT ) {
								$this->plain_text_transfer_encoding = $part->encoding;
								$this->plain_text_section = $index + 1;
							}
							
							if( $part->ifdisposition && strtolower($part->disposition) == 'attachment' && in_array($part->type, [TYPETEXT, TYPEAPPLICATION, TYPEAUDIO, TYPEIMAGE, TYPEVIDEO]) ) {
								$attachement_section = [
									'section' => $index + 1,
									'encoding' => $part->encoding,
									'filename' => $this->uid . '-' . (new DateTime())->getTimestamp() . 'txt'
								];

								if( $part->ifdparameters ) {
									foreach( $part->dparameters as $dparameter ) {
										if( strtolower($dparameter->attribute) == 'filename' ) {
											$attachement_section['filename'] = $dparameter->value;
										}
									}
								}
								else if( $part->ifdescription ) {
									$attachement_section['filename'] = $part->description;
								}

								$this->attachements_sections[] = $attachement_section;
							}
						}
					}					
				}

				$this->structure_fetched = true;
			}
		}

		private function getHeaderInfo() {
			if( ! $this->header_fetched ) {
				$this->header = imap_rfc822_parse_headers(imap_fetchbody( $this->imap_handler, $this->uid, $this->header_section, FT_UID & FT_PEEK ));

				$this->header_fetched = true;
			}
		}

		/**
		 * @return array
		 */
		public function getStructure() {
			$this->getStruct();

			return $this->structure;
		}

		/**
		 * @return array
		 */
		public function getCC() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->cc as $cc ) {
				$name = $cc->personal;
				$email = $cc->mailbox . '@' . $cc->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return array
		 */
		public function getBCC() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->bcc as $bcc ) {
				$name = $bcc->personal;
				$email = $bcc->mailbox . '@' . $bcc->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return array
		 */
		public function getReplyTo() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->reply_to as $reply_to ) {
				$name = $reply_to->personal;
				$email = $reply_to->mailbox . '@' . $reply_to->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return array
		 */
		public function getSender() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->sender as $sender ) {
				$name = $sender->personal;
				$email = $sender->mailbox . '@' . $sender->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return array
		 */
		public function getReturnPath() {
			$this->getHeaderInfo();

			$emails = [];

			foreach( $this->header->return_path as $return_path ) {
				$name = $return_path->personal;
				$email = $return_path->mailbox . '@' . $return_path->host;

				$emails[] = new Email( $name, $email );
			}

			return $emails;
		}

		/**
		 * @return string
		 */
		public function getDate() {
			$this->getHeaderInfo();

			return (new DateTime( $this->header->date ))->format('Y-m-d H:i:s');
		}

		/**
		 * @return string
		 */
		public function getSubject() {
			$this->getHeaderInfo();

			return imap_utf8($this->header->subject);
		}

		/**
		 * @return null|string
		 */
		public function getInReplyTo() {
			$this->getHeaderInfo();

			$property = 'in_reply_to';

			return property_exists($this->header, $property) ? (string) $this->header->${$property} : null;
		}

		/**
		 * @return string
		 */
		public function getMessageId() {
			$this->getHeaderInfo();

			return (string) $this->header->message_id;
		}

		/**
		 * @return array
		 */
		public function getReferences() {
			$this->getHeaderInfo();

			$references = property_exists($this->header, 'references') ? explode(' ', $this->header->references) : [];

			return $references;
		}

		/**
		 * @return bool
		 */
		public function isAnswered() {
			$this->getHeaderInfo();

			return $this->header->Answered === 'A';
		}

		/**
		 * @return bool
		 */
		public function isDeleted() {
			$this->getHeaderInfo();

			return $this->header->Deleted === 'D';
		}

		/**
		 * @return bool
		 */
		public function isDraft() {
			$this->getHeaderInfo();

			return $this->header->Draft === 'X';
		}

		/**
		 * @return int
		 */
		public function getMsgno() {
			$this->getHeaderInfo();

			return (int) $this->header->Msgno;
		}

		/**
		 * @return array
		 */
		public function getAttachments() {
			$this->getStruct();

			$attachments = [];

			foreach( $this->attachements_sections as $item ) {
				$attachments[] = new Attachment( $this->imap_handler, $this->uid, $item['section'], $item['encoding'], $item['filename'] );
			}

			return $attachments;
		}
	}
?>