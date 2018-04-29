<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ytake\KsqlClient\Query\{
    Ksql, Status
};
use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Result\AbstractResult;

/**
 * Class RestClientTest
 */
class RestClientTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldReturnRestClientInstance(): void
    {
        $client = new RestClient("http://localhost:8088");
        $this->assertInstanceOf(RestClient::class, $client);
    }

    public function testShouldBeCommandStatusesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/status.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Status());
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\CommandStatuses $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\CommandStatuses::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\CommandStatus::class,
            $entity->fullStatuses()
        );
    }

    public function testShouldBeKsqlEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/ksql.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Ksql('SHOW QUERIES;'));
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\AbstractKsql::class,
            $entity->getKsql()
        );
    }

    public function testShouldBeDescKsqlEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/desc.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Ksql('SHOW QUERIES;'));
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\AbstractKsql::class,
            $entity->getKsql()
        );
    }

    public function testShouldBeCommandStatusEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/single_status.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\CommandStatus $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\CommandStatus::class,
            $entity
        );
        $this->assertSame('QUEUED', $entity->getStatus());
        $this->assertSame('Statement written to command topic', $entity->getMessage());
    }

    public function testShouldBeErrorMessageEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/statement_error.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\CommandStatus $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\KsqlStatementErrorMessage[] $message */
        $message = $entity->getKsql();
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\KsqlStatementErrorMessage::class,
            $message
        );
        $this->assertSame(
            'SELECT NOW();',
            $message[0]->getStatementText()
        );
        $this->assertSame(
            "ServerError:io.confluent.ksql.parser.exception.ParseFailedException\r\nCaused by: null",
            trim($message[0]->getErrorMessage()->getMessage())
        );
    }

    public function testShouldReturnErrorResult(): void
    {
        $mock = new MockHandler([
            new Response(201, [], file_get_contents(realpath(__DIR__ . '/resources/error.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractResult::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlErrorMessage::class,
            $entity
        );
        $this->assertSame('HTTP 405 Method Not Allowed', $entity->getMessage());
    }

    /**
     * @expectedException \Ytake\KsqlClient\Exception\KsqlRestClientException
     */
    public function testShouldThrowClientException(): void
    {
        $mock = new MockHandler([
            new Response(405, [], file_get_contents(realpath(__DIR__ . '/resources/error.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        )->result();
    }
}
