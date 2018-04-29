<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use Ytake\KsqlClient\Entity\AbstractKsql;
use Ytake\KsqlClient\Entity\Description;
use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Entity\FieldSchema;
use Ytake\KsqlClient\Entity\KsqlCollection;
use Ytake\KsqlClient\Entity\KsqlErrorMessage;
use Ytake\KsqlClient\Entity\KsqlStatementErrorMessage;
use Ytake\KsqlClient\Entity\Queries;
use Ytake\KsqlClient\Entity\RunningQuery;

/**
 * Class KsqlResult
 */
class KsqlResult extends AbstractResult
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
    }
}
