<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\SourceDescription;
use Istyle\KsqlClient\Entity\SourceDescriptionEntity;
use Istyle\KsqlClient\Entity\RunningQuery;
use Istyle\KsqlClient\Entity\EntityQueryId;
use Istyle\KsqlClient\Query\QueryId;

/**
 * Class SourceDescriptionMapper
 */
final class SourceDescriptionMapper implements ResultInterface
{
    use RecursiveFieldTrait;

    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $description = $rows['sourceDescription'];

        $sourceDescription = new SourceDescription(
            $description['name'],
            $this->getQueries($description['readQueries'] ?? []),
            $this->getQueries($description['writeQueries'] ?? []),
            $this->parentFields($description['fields'] ?? []),
            $description['type'],
            $description['key'],
            $description['timestamp'],
            $description['statistics'],
            $description['errorStats'],
            $description['extended'],
            $description['partitions'],
            $description['replication']
        );
        return new SourceDescriptionEntity(
            $rows['statementText'],
            $sourceDescription
        );
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function getQueries(array $rows): array
    {
        $queries = [];
        foreach ($rows as $row) {
            $queries[] = new RunningQuery(
                $row['queryString'],
                $row['sinks'],
                new EntityQueryId(
                    new QueryId($row['id'])
                )
            );
        }
        return $queries;
    }
}
