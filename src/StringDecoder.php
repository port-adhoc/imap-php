<?php
	namespace PortAdhoc\Imap;

	use Exception;
	use PortAdhoc\Imap\Encoding;

	class StringDecoder {
		public static function getDecodedString( $string, $encoding, $encode_to ) {
			$decoded = '';

			switch( $encoding ) {
				case ENC7BIT:
					$decoded = mb_convert_encoding( $string, '7bit', $encode_to );

					break;

				case ENC8BIT: 
					$decoded = mb_convert_encoding( $string, '8bit', $encode_to );

					break;

				case ENCBINARY:
					throw new Exception('BINARY decoding not supported');

					break;

				case ENCBASE64:
					$decoded = mb_convert_encoding( $string, 'BASE64', $encode_to );

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