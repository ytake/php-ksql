# php-ksql
kafka ksql for php

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ytake/php-ksql/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ytake/php-ksql/?branch=master)
[![Build Status](https://travis-ci.org/ytake/php-ksql.svg?branch=master)](https://travis-ci.org/ytake/php-ksql)
[![StyleCI](https://styleci.io/repos/131283937/shield?branch=master)](https://styleci.io/repos/131283937)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1fe0cd22-6b08-4213-a99e-52fbc1911d9e/mini.png)](https://insight.sensiolabs.com/projects/1fe0cd22-6b08-4213-a99e-52fbc1911d9e)

## Usage

### Query KSQL

```bash
<?php

use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Query\Ksql;

$client = new RestClient(
    "http://localhost:8088"
);
$result = $client->requestQuery(
    new Ksql('DESCRIBE users_original;')
);
$result->result();

// return \Ytake\KsqlClient\Entity\Description object
```

### Client for Stream Response

```bash
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
);
$result->result();

```
