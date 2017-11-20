<?php
	require( __DIR__ . '/../vendor/autoload.php' );

	use PortAdhoc\Imap\Imap;
	use PortAdhoc\Imap\NoPlainTextException;

	$imap = new Imap;

	$imap->server = 'example';
	$imap->port = 993;
	$imap->flags = ['imap', 'ssl'];
	$imap->user = 'example@example.com';
	$imap->password = 'example';
	$imap->mailbox = 'INBOX';
	$imap->start = '1';
	$imap->end = '*';

	$imap->connect();

	$message = $imap->getMessage( 6 );

	$attachements = $message->getAttachments();

	print_r($attachements[0]->getContent()); 
?>