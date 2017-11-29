<?php
	namespace PortAdhoc\Imap;

	use Exception;

	class StringDecoder {
		public static function getDecodedString( $string, $encoding ) {
			$decoded = '';

			switch( $encoding ) {
				case ENC7BIT:
					throw new Exception(sprintf("7BIT decoding not supported (uid: %s)", $this->uid));

					break;

				case ENC8BIT: 
					throw new Exception(sprintf("8BIT decoding not supported (uid: %s)", $this->uid));

					break;

				case ENCBINARY:
					throw new Exception(spritnf('BINARY decoding not supported (uid: %s)', $this->uid));

					break;

				case ENCBASE64:
					$decoded = base64_decode( $string );

					break;

				case ENCQUOTEDPRINTABLE:
					$decoded = quoted_printable_decode( $string );

					break;

				case ENCOTHER:
					break;

				default:
					break;
			}

			return $decoded;
		}
	}
?>