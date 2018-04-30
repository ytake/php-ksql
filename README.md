# Istyle\KsqlClient [istyle-inc/php-ksql]

Apache kafka / Confluent KSQL REST Client for php

[![Build Status](http://img.shields.io/travis/istyle-inc/php-ksql/master.svg?style=flat-square)](https://travis-ci.org/istyle-inc/php-ksql)
[![Coverage Status](http://img.shields.io/coveralls/istyle-inc/php-ksql/master.svg?style=flat-square)](https://coveralls.io/github/istyle-inc/php-ksql?branch=master)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/istyle-inc/php-ksql.svg?style=flat-square)](https://scrutinizer-ci.com/g/istyle-inc/php-ksql/?branch=master)
[![StyleCI](https://styleci.io/repos/131283937/shield?branch=master)](https://styleci.io/repos/131283937)

[![License](http://img.shields.io/packagist/l/istyle-inc/php-ksql.svg?style=flat-square)](https://packagist.org/packages/istyle-inc/php-ksql)
[![Latest Version](http://img.shields.io/packagist/v/istyle-inc/php-ksql.svg?style=flat-square)](https://packagist.org/packages/istyle-inc/php-ksql)
[![Total Downloads](http://img.shields.io/packagist/dt/istyle-inc/php-ksql.svg?style=flat-square)](https://packagist.org/packages/istyle-inc/php-ksql)

## What is KSQL

KSQL is the streaming SQL engine for Apache Kafka.

[What Is KSQL?](https://docs.confluent.io/current/ksql/docs/)


## Install

required >= PHP 7.1

```bash
$ composer require istyle-inc/php-ksql
```

## Usage

### Request Preset

| class |
|-------------------------------------|
| Istyle\KsqlClient\Query\CommandStatus |
| Istyle\KsqlClient\Query\Status |
| Istyle\KsqlClient\Query\ServerInfo |
| Istyle\KsqlClient\Query\Ksql |
| Istyle\KsqlClient\Query\Stream (for stream) |

[Syntax Reference](https://docs.confluent.io/current/ksql/docs/syntax-reference.html)

### Get Command Status

```php
<?php

use Istyle\KsqlClient\RestClient;
use Istyle\KsqlClient\Query\CommandStatus;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(
    new CommandStatus('MESSAGE_STREAM/create')
)->result();

```

### Get Statuses

```php
<?php

use Istyle\KsqlClient\RestClient;
use Istyle\KsqlClient\Query\Status;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(new Status())->result();

```

### Get KSQL Server Information

```php
<?php

use Istyle\KsqlClient\RestClient;
use Istyle\KsqlClient\Query\ServerInfo;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(new ServerInfo())->result();

```

### Query KSQL

```php
<?php

use Istyle\KsqlClient\RestClient;
use Istyle\KsqlClient\Query\Ksql;

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

use Istyle\KsqlClient\StreamClient;
use Istyle\KsqlClient\Query\Stream;
use Istyle\KsqlClient\StreamConsumable;
use Istyle\KsqlClient\Entity\StreamedRow;

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
