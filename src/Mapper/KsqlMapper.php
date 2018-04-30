<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Entity\Description;
use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\FieldSchema;
use Istyle\KsqlClient\Entity\KafkaTopicInfo;
use Istyle\KsqlClient\Entity\KafkaTopics;
use Istyle\KsqlClient\Entity\KsqlCollection;
use Istyle\KsqlClient\Entity\KsqlErrorMessage;
use Istyle\KsqlClient\Entity\KsqlStatementErrorMessage;
use Istyle\KsqlClient\Entity\Queries;
use Istyle\KsqlClient\Entity\RunningQuery;
use Istyle\KsqlClient\Exception\UnknownJsonObjectsException;

/**
 * Class KsqlResult
 */
class KsqlMapper extends AbstractMapper
{
    /**
     * @return EntityInterface|KsqlCollection
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );
        $collect = new KsqlCollection();
        foreach ($decode as $row) {
            $collect->addKsql($this->detectEntity($row));
        }

        return $collect;
    }

    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    protected function detectEntity(array $row): AbstractKsql
    {
        if (isset($row['queries'])) {
            $queries = [];
            foreach ($row['queries']['queries'] as $query) {
                $queries[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }

            return new Queries($row['queries']['statementText'], $queries);
        }
        if (isset($row['description'])) {
            $read = $write = $schema = [];
            foreach ($row['description']['readQueries'] as $query) {
                $read[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }
            foreach ($row['description']['writeQueries'] as $query) {
                $write[] = new RunningQuery(
                    $query['statementText'],
                    $query['sinks'],
                    $query['id']
                );
            }
            foreach ($row['description']['schema'] as $query) {
                $schema[] = new FieldSchema(
                    $query['name'],
                    $query['type']
                );
            }
            $description = $row['description'];

            return new Description(
                $row['description']['statementText'],
                $description['name'],
                $read,
                $write,
                $schema,
                $description['type'],
                $description['key'],
                $description['timestamp'],
                $description['statistics'],
                $description['errorStats'],
                $description['extended'],
                $description['replication'],
                $description['partitions']
            );
        }
        if (isset($row['error'])) {
            if (isset($row['error']['errorMessage'])) {
                $errorMessage = $row['error']['errorMessage'];

                return new KsqlStatementErrorMessage(
                    $row['error']['statementText'],
                    new KsqlErrorMessage($errorMessage['message'], $errorMessage['stackTrace'])
                );
            }
        }
        if (isset($row['kafka_topics'])) {
            $topics = [];
            foreach ($row['kafka_topics']['topics'] as $topic) {
                $topics[] = new KafkaTopicInfo(
                    $topic['name'],
                    $topic['registered'],
                    (is_array($topic['replicaInfo'])) ? $topic['replicaInfo'] : [$topic['replicaInfo']],
                    $topic['consumerCount'],
                    $topic['consumerGroupCount']
                );
            }

            return new KafkaTopics($row['kafka_topics']['statementText'], $topics);
        }
        throw new UnknownJsonObjectsException('Unknown json objects.');
    }
}
