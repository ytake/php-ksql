<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ytake\KsqlClient\Query\{
    Status
};
use Ytake\KsqlClient\StreamClient;

class StreamClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \Ytake\KsqlClient\Exception\StreamQueryException
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testShouldBeCommandStatusesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/status.json'))),
        ]);
        $client = new StreamClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(new Status());
    }
}
