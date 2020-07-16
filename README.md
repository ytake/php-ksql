# Ytake\KsqlClient [ytake/php-ksql]

Apache kafka / Confluent KSQL REST Client for php

[![Build Status](http://img.shields.io/travis/ytake/php-ksql/master.svg?style=flat-square)](https://travis-ci.org/ytake/php-ksql)
[![Coverage Status](http://img.shields.io/coveralls/ytake/php-ksql/master.svg?style=flat-square)](https://coveralls.io/github/ytake/php-ksql?branch=master)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/php-ksql.svg?style=flat-square)](https://scrutinizer-ci.com/g/ytake/php-ksql/?branch=master)
[![StyleCI](https://styleci.io/repos/131283937/shield?branch=master)](https://styleci.io/repos/131283937)

[![License](http://img.shields.io/packagist/l/ytake/php-ksql.svg?style=flat-square)](https://packagist.org/packages/ytake/php-ksql)
[![Latest Version](http://img.shields.io/packagist/v/ytake/php-ksql.svg?style=flat-square)](https://packagist.org/packages/ytake/php-ksql)
[![Total Downloads](http://img.shields.io/packagist/dt/ytake/php-ksql.svg?style=flat-square)](https://packagist.org/packages/ytake/php-ksql)

## What is KSQL

KSQL is the streaming SQL engine for Apache Kafka.

[What Is KSQL?](https://docs.confluent.io/current/ksql/docs/)


## Install

required >= PHP 7.1

```bash
$ composer require ytake/php-ksql
```

## Usage

### Request Preset

| class |
|-------------------------------------|
| Ytake\KsqlClient\Query\CommandStatus |
| Ytake\KsqlClient\Query\Status |
| Ytake\KsqlClient\Query\ServerInfo |
| Ytake\KsqlClient\Query\Ksql |
| Ytake\KsqlClient\Query\Stream (for stream) |

[Syntax Reference](https://docs.confluent.io/current/ksql/docs/syntax-reference.html)

### Get Command Status

```php
<?php

use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Query\CommandStatus;
use Ytake\KsqlClient\Computation\CommandId;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(
    new CommandStatus(CommandId::fromString('stream/MESSAGE_STREAM/create'))
)->result();

```

### Get Statuses

```php
<?php

use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Query\Status;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(new Status())->result();

```

### Get KSQL Server Information

```php
<?php

use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Query\ServerInfo;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(new ServerInfo())->result();

```

### Query KSQL

```php
<?php

use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Query\Ksql;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(
    new Ksql('DESCRIBE users_original;')
)->result();

```

### Client for Stream Response

```php
<?php

use Ytake\KsqlClient\StreamClient;
use Ytake\KsqlClient\Query\Stream;
use Ytake\KsqlClient\StreamConsumable;
use Ytake\KsqlClient\Entity\StreamedRow;

$client = new StreamClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(
    new Stream(
        'SELECT * FROM testing',
        new class() implements StreamConsumable {
            public function __invoke(StreamedRow $row) 
            {
                // stream response consumer
            }
        }    
    )
)->result();
```
