<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Istyle\KsqlClient\Mapper\ResultInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Query\{
    Ksql, Status
};
use Istyle\KsqlClient\RestClient;
use Istyle\KsqlClient\Entity;

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

        $result = $client->requestQuery(new Ksql('DESCRIBE users;'));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\KsqlEntity::class,
            $entity->getKsql()
        );
    }

    public function testShouldBeQueryDescriptionKsqlEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/query_desc.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Ksql('EXPLAIN CSAS_STREAM2_0;'));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\KsqlEntity::class,
            $entity->getKsql()
        );
        /** @var Entity\QueryDescriptionEntity $result */
        $result = $entity->getKsql()[0];
        $this->assertInstanceOf(Entity\QueryDescriptionEntity::class, $result);
        $description = $result->getQueryDescription();
        $this->assertInstanceOf(
            Entity\QueryDescription::class,
            $description
        );
        $this->assertInstanceOf(
            Entity\EntityQueryId::class,
            $description->getEntityQueryId()
        );
        $this->assertSame('CSAS_STREAM2_0', $description->getEntityQueryId()->getId());
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
        /** @var \Istyle\KsqlClient\Entity\KsqlStatementErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlStatementErrorMessage::class,
            $entity
        );
        $this->assertSame($entity->getMessage(), 'SELECT and PRINT queries must use the /query endpoint');
        $this->assertSame(40002, $entity->getErrorCode());
    }

    public function testShouldReturnErrorResult(): void
    {
        $mock = new MockHandler([
            new Response(201, [], file_get_contents(realpath(__DIR__ . '/resources/generic_error.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Istyle\KsqlClient\Query\CommandStatus('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(ResultInterface::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlErrorMessage::class,
            $entity
        );
        $this->assertSame('The server returned an unexpected error.', $entity->getMessage());
    }

    /**
     * @expectedException \Istyle\KsqlClient\Exception\KsqlRestClientException
     */
    public function testShouldThrowClientException(): void
    {
        $mock = new MockHandler([
            new Response(405, [], file_get_contents(realpath(__DIR__ . '/resources/generic_error.json'))),
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
        $this->assertSame('5.0.1', $entity->getVersion());
        $this->assertNotEmpty($entity->getKafkaClusterId());
        $this->assertNotEmpty($entity->getKsqlServiceId());
    }

    public function testShouldReturnTablesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/tables.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Istyle\KsqlClient\Query\Ksql('SHOW TABLES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\TablesList $table */
        $table = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\TablesList::class,
            $table
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\SourceInfoTable::class,
            $table->getSourceInfoList()
        );
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
        $this->assertSame('_schemas', $info->getName());
        $this->assertSame(false, $info->getRegistered());
        $this->assertSame([1], $info->getReplicaInfo());
        $this->assertSame(0, $info->getConsumerCount());
        $this->assertSame(0, $info->getConsumerGroupCount());
    }

    public function testShouldReturnStreamsList(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/streamslist.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Istyle\KsqlClient\Query\Ksql("LIST STREAMS;"));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var  \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\StreamsList $topic */
        $topic = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\StreamsList::class,
            $topic
        );
        $list = $topic->getSourceInfoList();
        $this->assertContainsOnly(\Istyle\KsqlClient\Entity\SourceInfo::class, $list);
        $this->assertCount(1, $list);
        foreach ($list as $row) {
            $this->assertSame($row->getTopic(), 'ksql-testing');
            $this->assertSame($row->getName(), 'KSQLTESTING');
        }
    }

    public function testShouldReturnQueriesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/queries.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Istyle\KsqlClient\Query\Ksql('LIST QUERIES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\Queries $queries */
        $queries = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\Queries::class,
            $queries
        );
        $this->assertContainsOnlyInstancesOf(
            \Istyle\KsqlClient\Entity\RunningQuery::class,
            $queries->getQueries()
        );
    }

    public function testShouldReturnPropertiesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/properties.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Istyle\KsqlClient\Query\Ksql('LIST PROPERTIES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Istyle\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Istyle\KsqlClient\Entity\Properties $properties */
        $properties = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Istyle\KsqlClient\Entity\Properties::class,
            $properties
        );
    }

    public function testShouldReturnKsqlErrorMessage(): void
    {
        $mock = new MockHandler([
            new Response(201, [], file_get_contents(realpath(__DIR__ . '/resources/info.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            [],
            new Client(['handler' => HandlerStack::create($mock)])
        );
        /** @var Entity\KsqlErrorMessage $result */
        $result = $client->requestQuery(
            new \Istyle\KsqlClient\Query\ServerInfo()
        )->result();
        $this->assertSame($result->getErrorCode(), 201);
        $this->assertSame($result->getMessage(), 'The server returned an unexpected error.');
    }
}
