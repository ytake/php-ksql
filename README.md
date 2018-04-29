# php-ksql
kafka ksql for php

[![StyleCI](https://styleci.io/repos/131283937/shield?branch=master)](https://styleci.io/repos/131283937)

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
