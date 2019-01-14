<?php
declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Istyle\KsqlClient\Mapper\KsqlMapper;
use Istyle\KsqlClient\Entity\KsqlCollection;
use Istyle\KsqlClient\Entity\SourceDescription;
use Istyle\KsqlClient\Entity\RunningQuery;

class KsqlMapperTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldReturnDescriptionEntity(): void
    {
        $mapper = new KsqlMapper(new Response(200, [], $this->json()));
        /** @var KsqlCollection $result */
        $result = $mapper->result();
        $row = $result->getKsql()[0];
        /** @var SourceDescription $row */
        $this->assertInstanceOf(SourceDescription::class, $row);
        $this->assertEmpty($row->getStatementText());
        $this->assertEmpty($row->getErrorStats());
        $this->assertSame('MESSAGE', $row->getKey());
        $this->assertSame('MSGS', $row->getName());
        $this->assertContainsOnlyInstancesOf(RunningQuery::class, $row->getReadQueries());
        $this->assertSame(0, $row->getPartitions());
        $this->assertSame(0, $row->getReplication());
        /** @var RunningQuery $query */
        $query = $row->getReadQueries()[0];
        $this->assertSame('testing', $query->getId());
        $this->assertSame('', $query->getQueryString());
        $this->assertSame([], $query->getSinks());
    }

    protected function json(): string
    {
        return '[
  {
    "description": {
      "statementText": "",
      "name": "MSGS",
      "readQueries": [{
        "statementText": "",
        "sinks": [],
        "id": "testing"
      }],
      "writeQueries": [],
      "schema": [
        {
          "name": "ROWTIME",
          "type": "BIGINT"
        }
      ],
      "type": "TABLE",
      "key": "MESSAGE",
      "timestamp": "",
      "statistics": "",
      "errorStats": "",
      "extended": false,
      "serdes": "JSON",
      "kafkaTopic": "testing",
      "topology": "",
      "executionPlan": "",
      "replication": 0,
      "partitions": 0
    }
  }
]
';
    }
}
