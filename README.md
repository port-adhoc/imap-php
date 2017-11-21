[![PHP from Packagist](https://img.shields.io/packagist/php-v/port-adhoc/imap.svg)]()
[![Licence](https://img.shields.io/badge/licence-MIT-blue.svg)]()

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

[back to summary](#summary)

# Function list
- [`Imap::`](#imap)
  - [`construct`](#imap-construct)
  - [`properties`](#imap-properties)
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
- [`Email::`](#email)
  - [`properties`](#email-properties)

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
```