<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Ytake\KsqlClient\Mapper\ResultInterface;
use Ytake\KsqlClient\Mapper\AbstractMapper;
use Ytake\KsqlClient\Query\{
    Ksql, Status
};
use Ytake\KsqlClient\RestClient;
use Ytake\KsqlClient\Entity;
use Ytake\KsqlClient\Computation\CommandId;
use Ytake\KsqlClient\Properties\LocalProperties;
use Ytake\KsqlClient\Properties\LocalPropertyValidator;

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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Status());
        $this->assertInstanceOf(AbstractMapper::class, $result);
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

    public function testShouldBeDescKsqlEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/desc.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Ksql('DESCRIBE users;'));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\KsqlEntity::class,
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new Ksql('EXPLAIN CSAS_STREAM2_0;'));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\KsqlEntity::class,
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
        $this->assertNotCount(0, $description->getSinks());
        $this->assertContainsOnlyInstancesOf(
            Entity\FieldInfo::class, $description->getFields()
        );
        $this->assertNotEmpty($description->getExecutionPlan());
        $this->assertCount(0, $description->getOverriddenProperties());
        $this->assertNotCount(0, $description->getSources());
        $this->assertSame(
            "CREATE STREAM stream2 	WITH (kafka_topic='output-topic' , value_format='DELIMITED') 	AS SELECT * FROM stream1 WHERE LEN(message) > 2;",
            $description->getStatementText()
        );
        $this->assertNotEmpty($description->getTopology());
    }

    public function testShouldBeCommandStatusEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/single_status.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $properties = new LocalProperties(["ksql.streams.auto.offset.reset" => "earliest"],
            new LocalPropertyValidator());

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus(
                CommandId::fromString('a/MESSAGE_STREAM/create')
            ),
            $properties
        );

        $this->assertInstanceOf(AbstractMapper::class, $result);
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql('MESSAGE_STREAM/create')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlStatementErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlStatementErrorMessage::class,
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus(
                CommandId::fromString('testing/MESSAGE_STREAM/create')
            )
        );
        $this->assertInstanceOf(ResultInterface::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlErrorMessage $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlErrorMessage::class,
            $entity
        );
        $this->assertSame('The server returned an unexpected error.', $entity->getMessage());
    }

    /**
     * @expectedException \Ytake\KsqlClient\Exception\KsqlRestClientException
     */
    public function testShouldThrowClientException(): void
    {
        $mock = new MockHandler([
            new Response(405, [], file_get_contents(realpath(__DIR__ . '/resources/generic_error.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(
            new \Ytake\KsqlClient\Query\CommandStatus(
                CommandId::fromString('testing/MESSAGE_STREAM/create')
            )
        )->result();
    }

    public function testShouldReturnServerInfoEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/info.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Ytake\KsqlClient\Query\ServerInfo());
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\ServerInfo $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\ServerInfo::class,
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql('SHOW TABLES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\TablesList $table */
        $table = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\TablesList::class,
            $table
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\SourceInfoTable::class,
            $table->getSourceInfoList()
        );
    }

    public function testCanBeArrayForBasicAuth(): void
    {
        $mock = new MockHandler([new Response()]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->setAuthCredentials(
            new \Ytake\KsqlClient\AuthCredential('testing', 'testing')
        );
        $client->requestQuery(new \Ytake\KsqlClient\Query\ServerInfo());
        $request = $mock->getLastRequest();
        $this->assertInstanceOf(\Ytake\KsqlClient\AuthCredential::class, $client->getAuthCredentials());
        $this->assertSame('Basic dGVzdGluZzp0ZXN0aW5n', $request->getHeaderLine('Authorization'));

        $mock = new MockHandler([new Response()]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $client->requestQuery(new \Ytake\KsqlClient\Query\ServerInfo());
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Ytake\KsqlClient\Query\Ksql("SHOW TOPICS;"));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var  \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\KafkaTopics $topic */
        $topic = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KafkaTopics::class,
            $topic
        );
        $this->assertSame('SHOW TOPICS;', $topic->getStatementText());
        $list = $topic->getKafkaTopicInfoList();
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\KafkaTopicInfo::class,
            $list
        );
        /** @var \Ytake\KsqlClient\Entity\KafkaTopicInfo $info */
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(new \Ytake\KsqlClient\Query\Ksql("LIST STREAMS;"));
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var  \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\StreamsList $topic */
        $topic = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\StreamsList::class,
            $topic
        );
        $list = $topic->getSourceInfoList();
        $this->assertContainsOnly(\Ytake\KsqlClient\Entity\SourceInfo::class, $list);
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
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql('LIST QUERIES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\Queries $queries */
        $queries = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\Queries::class,
            $queries
        );
        $this->assertContainsOnlyInstancesOf(
            \Ytake\KsqlClient\Entity\RunningQuery::class,
            $queries->getQueries()
        );
        /** @var \Ytake\KsqlClient\Entity\RunningQuery $query */
        $query = $queries->getQueries()[0];
        $this->assertSame('CSAS_STREAM2_0', $query->getId()->getId());
        $this->assertNotCount(0, $query->getSinks());
    }

    public function testShouldReturnPropertiesEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/properties.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );

        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql('LIST PROPERTIES;')
        );
        $this->assertInstanceOf(AbstractMapper::class, $result);
        /** @var \Ytake\KsqlClient\Entity\KsqlCollection $entity */
        $entity = $result->result();
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $entity
        );
        /** @var \Ytake\KsqlClient\Entity\Properties $properties */
        $properties = $entity->getKsql()[0];
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\Properties::class,
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
            new Client(['handler' => HandlerStack::create($mock)])
        );
        /** @var Entity\KsqlErrorMessage $result */
        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\ServerInfo()
        )->result();
        $this->assertSame($result->getErrorCode(), 201);
        $this->assertSame($result->getMessage(), 'The server returned an unexpected error.');

        $client = new RestClient(
            "http://localhost:8088",
            new Client([
                'handler' => HandlerStack::create(new MockHandler([
                    new Response(401, [], null),
                ]))
            ])
        );
        $client->setOptions(['http_errors' => false]);
        /** @var Entity\KsqlErrorMessage $result */
        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\ServerInfo()
        )->result();
        $this->assertSame($result->getErrorCode(), 401);
        $this->assertSame(
            $result->getMessage(),
            'Could not authenticate successfully with the supplied credentials.'
        );
        $client = new RestClient(
            "http://localhost:8088",
            new Client([
                'handler' => HandlerStack::create(new MockHandler([
                    new Response(403, [], null),
                ]))
            ])
        );
        $client->setOptions(['http_errors' => false]);
        /** @var Entity\KsqlErrorMessage $result */
        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\ServerInfo()
        )->result();
        $this->assertSame($result->getErrorCode(), 403);
        $this->assertSame(
            $result->getMessage(),
            'You are forbidden from using this cluster.'
        );
        $client = new RestClient(
            "http://localhost:8088",
            new Client([
                'handler' => HandlerStack::create(new MockHandler([
                    new Response(405, [], null),
                ]))
            ])
        );
        $client->setOptions(['http_errors' => false]);
        /** @var Entity\KsqlErrorMessage $result */
        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\ServerInfo()
        )->result();
        $this->assertSame($result->getErrorCode(), 405);
        $this->assertSame(
            $result->getMessage(),
            'The server returned an unexpected error.'
        );
    }

    public function testShouldReturnCurrentStatusEntity(): void
    {
        $mock = new MockHandler([
            new Response(200, [], file_get_contents(realpath(__DIR__ . '/resources/currentStatus.json'))),
        ]);
        $client = new RestClient(
            "http://localhost:8088",
            new Client(['handler' => HandlerStack::create($mock)])
        );
        $result = $client->requestQuery(
            new \Ytake\KsqlClient\Query\Ksql(
                "CREATE STREAM ksqltesting (message VARCHAR) WITH (KAFKA_TOPIC='ksql-testing',  VALUE_FORMAT='JSON');"
            )
        )->result();
        /** @var Entity\KsqlCollection $result */
        $this->assertInstanceOf(
            \Ytake\KsqlClient\Entity\KsqlCollection::class,
            $result
        );
        /** @var Entity\CommandStatusEntity $commandStatus */
        $commandStatus = $result->getKsql()[0];
        $this->assertSame(-1, $commandStatus->getCommandSequenceNumber());
        $this->assertSame(
            "CREATE STREAM ksqltesting (message VARCHAR) WITH (KAFKA_TOPIC='ksql-testing',  VALUE_FORMAT='JSON');",
            $commandStatus->getStatementText()
        );
        $this->assertInstanceOf(CommandId::class, $commandStatus->getCommandId());
        $this->assertSame('Stream created', $commandStatus->getCommandStatus()->getMessage());
        $this->assertSame('SUCCESS', $commandStatus->getCommandStatus()->getStatus());
    }
}
