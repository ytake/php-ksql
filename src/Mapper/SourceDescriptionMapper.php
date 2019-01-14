<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\FieldInfo;
use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\SchemaInfo;
use Istyle\KsqlClient\Entity\SourceDescription;
use Istyle\KsqlClient\Entity\SourceDescriptionEntity;
use Istyle\KsqlClient\Entity\RunningQuery;
use Istyle\KsqlClient\Entity\EntityQueryId;
use Istyle\KsqlClient\Query\QueryId;

use function is_null;

/**
 * Class SourceDescriptionMapper
 */
final class SourceDescriptionMapper implements ResultInterface
{
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
     * @return array|null
     */
    private function parentFields(array $rows): ?array
    {
        $fields = [];
        foreach ($rows as $row) {
            $schema = $row['schema'];
            $fields[] = new FieldInfo(
                $row['name'],
                new SchemaInfo(
                    $schema['type'],
                    $this->recursiveFields($schema['fields']),
                    $this->recursiveSchemaInfo($schema['memberSchema'])
                )
            );
        }
        return $fields;
    }

    /**
     * @param array|null $rows
     *
     * @return FieldInfo|null
     */
    private function recursiveFields(?array $rows): ?array
    {
        if (!is_null($rows)) {
            $fields = [];
            foreach ($rows as $row) {
                if (!is_null($row['memberSchema'])) {
                    $fields[] = new FieldInfo(
                        $row['name'],
                        $this->recursiveSchemaInfo($row['memberSchema'] ?? [])
                    );
                }
            }
            return $fields;
        }
        return null;
    }

    /**
     * @param array|null $rows
     *
     * @return SchemaInfo|null
     */
    private function recursiveSchemaInfo(?array $rows): ?SchemaInfo
    {
        if (is_null($rows)) {
            return null;
        }
        return new SchemaInfo($rows['type'], $this->recursiveFields($rows['fields']), $rows['memberSchema']);
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
