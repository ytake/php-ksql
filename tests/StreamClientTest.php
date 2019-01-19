<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use Istyle\KsqlClient\Entity\StreamedRow;
use Istyle\KsqlClient\Entity\StreamedRows;
use Istyle\KsqlClient\Query\{
    Status, Stream
};
use Istyle\KsqlClient\StreamClient;
use Istyle\KsqlClient\StreamConsumable;

class StreamClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException \Istyle\KsqlClient\Exception\StreamQueryException
     */
    public function testShouldThrowStreamQueryException(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/status.json'))),
        ]);
        $client = new StreamClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(new Status());
    }

    public function testShouldConsumeStreamResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/stream_query.json'))),
        ]);
        $client = new StreamClient(
            "http://localhost:8088",
            new Client([
                'handler' => HandlerStack::create($mock),
                'stream'  => true,
            ])
        );
        $rows = $client->requestQuery(
            new Stream(
                'SELECT * FROM testings;',
                new class() implements StreamConsumable
                {
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
