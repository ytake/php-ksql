<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Query\{
    Ksql, Status
};
use Istyle\KsqlClient\RestClient;

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

    public function testShouldReturnSameServerAddress(): void
    {
        $client = new RestClient("http://localhost:8088");
        $this->assertSame("http://localhost:8088", $client->getServerAddress());
        $client->setServerAddress('http://testing.app');
        $this->assertSame("http://testing.app", $client->getServerAddress());
    }

    public function testCanAppendArrayForClientOption(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/status.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->setOptions([
            'headers'        => ['Accept-Encoding' => 'gzip'],
            'decode_content' => false,
        ]);
        $client->requestQuery(new Status());
        $this->assertSame(['gzip'], $mock->getLastRequest()->getHeader('accept-encoding'));
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
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\CommandStatuses $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\CommandStatuses::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\CommandStatus::class,
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
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\AbstractKsql::class,
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
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\AbstractKsql::class,
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
            new \Istyle\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\CommandStatus $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\CommandStatus::class,
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
            new \Istyle\KsqlClient\Query\Ksql('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\CommandStatus $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\KsqlStatementErrorMessage[] $message */
        $message = $entity->getKsql();
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\KsqlStatementErrorMessage::class,
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
            new \Istyle\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlErrorMessage::class,
            $entity
        );
        $this->assertSame('HTTP 405 Method Not Allowed', $entity->getMessage());
    }

    /**
     * @expectedException \Istyle\KsqlClient\Exception\KsqlRestClientException
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
            new \Istyle\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        )->result();
    }

    public function testShouldReturnServerInfoEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/info.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Istyle\KsqlClient\Query\ServerInfo());
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\ServerInfo $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\ServerInfo::class,
            $entity
        );
        $this->assertSame('4.1.0', $entity->getVersion());
        $this->assertEmpty($entity->getKafkaClusterId());
        $this->assertEmpty($entity->getKsqlServiceId());
    }

    public function testCanBeArrayForBasicAuth(): void
    {
        $mock = new MockHandler([new Response()]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->setAuthCredentials(
            new \Istyle\KsqlClient\AuthCredential('testing', 'testing')
        );
        $client->requestQuery(new \Istyle\KsqlClient\Query\ServerInfo());
        $request = $mock->getLastRequest();
        $this->assertInstanceOf(\Istyle\KsqlClient\AuthCredential::class, $client->getAuthCredentials());
        $this->assertSame('Basic dGVzdGluZzp0ZXN0aW5n', $request->getHeaderLine('Authorization'));

        $mock = new MockHandler([new Response()]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(new \Istyle\KsqlClient\Query\ServerInfo());
        $request = $mock->getLastRequest();
        $this->assertNotSame('Basic dGVzdGluZzp0ZXN0aW5n', $request->getHeaderLine('Authorization'));
        $this->assertNull($client->getAuthCredentials());
    }

    public function testShouldReturnKafkaTopics(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/kafka_topics.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Istyle\KsqlClient\Query\Ksql("SHOW TOPICS;"));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var  \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\KafkaTopics $topic */
        $topic = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KafkaTopics::class,
            $topic
        );
        $this->assertSame('SHOW TOPICS;', $topic->getStatementText());
        $list = $topic->getKafkaTopicInfoList();
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\KafkaTopicInfo::class,
            $list
        );
        /** @var \Istyle\KsqlClient\Entity\KafkaTopicInfo $info */
        $info = $list[0];
        $this->assertSame('__confluent.support.metrics', $info->getName());
        $this->assertSame('false', $info->getRegistered());
        $this->assertSame(['1'], $info->getReplicaInfo());
        $this->assertSame(0, $info->getConsumerCount());
        $this->assertSame(0, $info->getConsumerGroupCount());

    }
}
