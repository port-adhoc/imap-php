<?php
	namespace PortAdhoc\Imap;

	use Exception;
	use PortAdhoc\Imap\Encoding;

	class StringDecoder {
		public static function getDecodedString( $string, $charset, $encoding, $encode_to ) {
			$decoded = '';

			switch( $encoding ) {
				case ENC7BIT:
					$decoded = mb_convert_encoding( mb_convert_encoding( $string, $charset, '7bit' ), $encode_to, $charset );

					break;

				case ENC8BIT: 
					$decoded = mb_convert_encoding( mb_convert_encoding( $string, $charset, '7bit' ), $encode_to, $charset );

					break;

				case ENCBINARY:
					throw new Exception('BINARY decoding not supported');

					break;

				case ENCBASE64:
					$decoded = mb_convert_encoding( base64_decode($string), $encode_to, $charset );

					break;

				case ENCQUOTEDPRINTABLE:
					$decoded = mb_convert_encoding( quoted_printable_decode($string), $encode_to, $charset );

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