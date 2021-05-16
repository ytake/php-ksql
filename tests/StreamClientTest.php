<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Entity\StreamedRow;
use Ytake\KsqlClient\Entity\StreamedRows;
use Ytake\KsqlClient\Exception\StreamQueryException;
use Ytake\KsqlClient\Query\{Status, Stream};
use Ytake\KsqlClient\StreamClient;
use Ytake\KsqlClient\StreamConsumable;

use function file_get_contents;
use function realpath;

final class StreamClientTest extends TestCase
{
    public function testShouldThrowStreamQueryException(): void
    {
        $this->expectException(StreamQueryException::class);
        $mock = new MockHandler(
            [
                new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/status.json'))),
            ]
        );
        $client = new StreamClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(new Status());
    }

    public function testShouldConsumeStreamResponse(): void
    {
        $mock = new MockHandler(
            [
                new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/stream_query.json'))),
            ]
        );
        $client = new StreamClient(
            "http://localhost:8088",
            new Client(
                [
                    'handler' => HandlerStack::create($mock),
                    'stream' => true,
                ]
            )
        );
        $rows = $client->requestQuery(
            new Stream(
                'SELECT * FROM testings;',
                new class() implements StreamConsumable {
                    public function __invoke(StreamedRow $row)
                    {
                        $expect = [1525094515273, null, 'testing'];
                        Assert::assertEquals($expect, $row->getRow()->getColumns());
                        Assert::assertNull($row->getRow()->getColumnValue(1));
                        Assert::assertEmpty($row->getKsqlErrorMessage()->getMessage());
                        Assert::assertSame([], $row->getKsqlErrorMessage()->getStackTrace());
                    }
                }
            )
        )->result();
        /** @var StreamedRows $rows */
        $this->assertInstanceOf(StreamedRows::class, $rows);
        $this->assertContainsOnlyInstancesOf(StreamedRow::class, $rows->getRow());
    }

    public function testShouldUseStreamOption(): void
    {
        $client = new StreamClient("http://localhost:8088");
        $config = $client->getClient()->getConfig();
        $this->assertTrue($config['stream']);
    }
}
