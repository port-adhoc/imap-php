<?php
	namespace PortAdhoc\Imap;

	use Exception;

	class StringDecoder {
		public static function getDecodedString( $string, $encoding ) {
			$decoded = '';

			switch( $encoding ) {
				case ENC7BIT:
					throw new Exception("7BIT decoding not supported");

					break;

				case ENC8BIT: 
					throw new Exception("8BIT decoding not supported");

					break;

				case ENCBINARY:
					throw new Exception('BINARY decoding not supported');

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