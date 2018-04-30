# php-ksql Istyle\KsqlClient

Apache kafka / Confluent KSQL REST Client for php

[What Is KSQL?](https://docs.confluent.io/current/ksql/docs/)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ytake/php-ksql/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ytake/php-ksql/?branch=master)
[![Build Status](https://travis-ci.org/ytake/php-ksql.svg?branch=master)](https://travis-ci.org/ytake/php-ksql)
[![StyleCI](https://styleci.io/repos/131283937/shield?branch=master)](https://styleci.io/repos/131283937)

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
