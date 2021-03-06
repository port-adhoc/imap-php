![Packagist minimum PHP version](https://img.shields.io/packagist/php-v/port-adhoc/imap.svg)
![Packagist version](https://img.shields.io/packagist/v/port-adhoc/imap.svg)
![Packagist licence](https://img.shields.io/packagist/l/port-adhoc/imap.svg)

# imap-php
Uid oriented Imap class

# Summary
- [Installation](#installation)
- [Usage](#usage)
- [Function list](#function-list)

# Installation

In your repository :

```bash
composer require port-adhoc/imap
```

[back to summary](#summary)

# Usage
- [Example 1 : traversing a whole mailbox and retriving its content](#example-1--traversing-a-whole-mailbox-and-retriving-its-content)

## Example 1 : traversing a whole mailbox and retriving its content
```php
use PortAdhoc\Imap\Imap;
use PortAdhoc\Imap\Encoding;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX';
$imap->start = '1';
$imap->end = '*';

$imap->connect();

$messages = $imap->getMessages();

foreach( $messages as $message ) {
  $subject = $message->getSubject();
  $date = $message->getDate();
  $from = $message->getFrom();
  $to = $message->getTo();
  $cc = $message->getCC();
  $bcc = $message->getBCC();
  $text = $message->getPlainText( Encoding::UTF_8 );
  $html = $message->getPlainText( Encoding::UTF_8 );
  $attachments = $message->getAttachments();
}
```

[back to summary](#summary)

# Function list
- [`Attachment::`](#attachment)
  - [`getName`](#getname)
  - [`getContent`](#getcontent)
- [`Email::`](#email)
  - [`properties`](#email-properties)
    - [`name`](#email-property-name)
    - [`email`](#email-property-email)
- [`Imap::`](#imap)
  - [`construct`](#imap-construct)
  - [`properties`](#imap-properties)
    - [`server`](#imap-property-server)
    - [`port`](#imap-property-port)
    - [`flags`](#imap-property-flags)
    - [`user`](#imap-property-user)
    - [`password`](#imap-property-password)
    - [`mailbox`](#imap-property-mailbox)
    - [`start`](#imap-property-start)
    - [`end`](#imap-property-end)
    - [`connection_time`](#imap-property-connection_time)
    - [`message_fetching_time`](#imap-property-message_fetching_time)
  - [`connect`](#connect)
  - [`getConnectionString`](#getconnectionstring)
  - [`getMessage`](#getmessage)
  - [`getMessages`](#getmessages)
- [`Message::`](#message)
  - [`getAttachments`](#getattachments)
  - [`getBCC`](#getbcc)
  - [`getCC`](#getcc)
  - [`getDate`](#getdate)
  - [`getFrom`](#getfrom)
  - [`getHtml`](#gethtml)
  - [`getImap`](#getimap)
  - [`getInReplyTo`](#getinreplyto)
  - [`getMessageId`](#getmessageid)
  - [`getMsgno`](#getmsgno)
  - [`getPlainText`](#getplaintext)
  - [`getReferences`](#getreferences)
  - [`getReplyTo`](#getreplyto)
  - [`getReturnPath`](#getreturnpath)
  - [`getSender`](#getsender)
  - [`getStructure`](#getstructure)
  - [`getSubject`](#getsubject)
  - [`getTo`](#getto)
  - [`getUid`](#getuid)
  - [`isAnswered`](#isAnswered)
  - [`isDeleted`](#isdeleted)
  - [`isDraft`](#isdraft)

## Attachment

### getName

```php
public function getName(): string
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '500'; // uid
$imap->end = '500'; // uid

$imap->connect();

$uid = 500;

$message = $imap->getMessage( $uid );

$attachments = $message->getAttachments();

foreach( $attachments as $attachment ) {
	$name = $attachment->getName();

	echo $name;
}
```

Result:

```bash
> php script.php
reporting result quarter 4.xlsx
```

### getContent

```php
public function getContent(): string
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '500'; // uid
$imap->end = '500'; // uid

$imap->connect();

$uid = 500;

$message = $imap->getMessage( $uid );

$attachments = $message->getAttachments();

foreach( $attachments as $attachment ) {
	$name = $attachment->getName();
	$content = $attachement->getContent();
	$path = __DIR__ . '/' . $name; // inside the current repository

	file_put_contents( $path, $content );
}
```

Result:

```bash
/vendor
composer.json
composer.lock
script.php
reporting result quarter 4.xlsx
```

## Imap

### Imap construct
```php
public function __construct(): PortAdhoc\Imap\Imap
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;
```

[back to function list](#function-list)

[back to summary](#summary)

### Imap properties
```php
public string $server;
public int $port;
public string $user;
public string $password;
public string $mailbox;
public int $connection_time;
public int $message_fetching_time;
public string $start; // uid
public string $end; // uid
```

[back to function list](#function-list)

[back to summary](#summary)

### connect
```php
public function connect(): PortAdhoc\Imap\Imap
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '1'; // uid
$imap->end = '500'; // uid

$imap->connect();
```

[back to function list](#function-list)

[back to summary](#summary)

### getMessage
```php
public function getMessage( int $uid ): PortAdhoc\Imap\Message
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '1'; // uid
$imap->end = '500'; // uid

$imap->connect();

$uid = 500;

$message = $imap->getMessage( $uid );
```

[back to function list](#function-list)

[back to summary](#summary)

### getMessages
```php
public function getMessages(): PortAdhoc\Imap\Message[]
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '1'; // uid
$imap->end = '500'; // uid

$imap->connect();

$messages = $imap->getMessages();

foreach( $messages as $message ) {
  // ...
}
```

[back to function list](#function-list)

[back to summary](#summary)

### getConnectionString
```php
public function getConnectionString(): string
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX'; // or INBOX/folder
$imap->start = '1'; // uid
$imap->end = '500'; // uid

$cs = $imap->getConnectionString();

echo $cs;
```

Result:

```bash
> php script.php
{example.host.com:993/imap/ssl/readonly}INBOX/folder
```

[back to function list](#function-list)

[back to summary](#summary)

## Email

### Email properties
```php
public $email;
public $name;
```

Example:

```php
use PortAdhoc\Imap\Imap;

$imap = new Imap;

$imap->server = 'example.host.com';
$imap->port = 993;
$imap->flags = ['imap', 'ssl', 'readonly'];
$imap->user = 'example@host.com';
$imap->password = 'example';
$imap->mailbox = 'INBOX';

$uid = 500;

$message = $imap->getMessage( $uid );

$from = $message->getFrom();

$email = $from->email;
$name = $from->name;

echo $name . ' ' . $email;
```

Result:

```bash
> php script.php
John Doe john.doe@contoso.com
```

[back to function list](#function-list)

[back to summary](#summary)