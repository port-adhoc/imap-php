<?php
	namespace PortAdhoc\Imap;

	use Exception;
	use PortAdhoc\Imap\Encoding;

	class StringDecoder {
		public static function getDecodedString( $string, $encoding, $encode_to ) {
			$decoded = '';

			switch( $encoding ) {
				case ENC7BIT:
					$decoded = mb_convert_encoding( $string, $encode_to, '7bit' );

					break;

				case ENC8BIT: 
					$decoded = mb_convert_encoding( $string, $encode_to, '8bit' );

					break;

				case ENCBINARY:
					throw new Exception('BINARY decoding not supported');

					break;

				case ENCBASE64:
					$decoded = mb_convert_encoding( $string, $encode_to, 'BASE64' );

					break;

				case ENCQUOTEDPRINTABLE:
					$decoded = mb_convert_encoding(quoted_printable_decode( $string ), $encode_to);

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