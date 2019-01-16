<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\FieldInfo;
use Istyle\KsqlClient\Entity\SchemaInfo;

/**
 * Trait RecursiveFieldTrait
 */
trait RecursiveFieldTrait
{
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
}
