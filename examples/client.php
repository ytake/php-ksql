<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Istyle\KsqlClient\Query\Stream;
use Istyle\KsqlClient\StreamClient;
use Istyle\KsqlClient\StreamConsumable;
use Istyle\KsqlClient\Entity\StreamedRow;

$client = new StreamClient('http://localhost:8088');
$client->requestQuery(
    new Stream(
        'SELECT * FROM message_stream;',
        new class() implements StreamConsumable
        {
            public function __invoke(StreamedRow $row)
            {
                var_dump($row);
            }
        }
    )
)->result();
