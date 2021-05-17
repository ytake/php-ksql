<?php

declare(strict_types=1);

namespace Tests\Mapper;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Entity\FieldInfo;
use Ytake\KsqlClient\Entity\SchemaInfo;
use Ytake\KsqlClient\Entity\SourceDescriptionEntity;
use Ytake\KsqlClient\Mapper\KsqlMapper;

use function file_get_contents;

final class SourceDescriptionTest extends TestCase
{
    public function testShouldReturnSourceDescriptionEntity(): void
    {
        $mapper = new KsqlMapper(
            new Response(
                200,
                [],
                file_get_contents(__DIR__ . '/../resources/source_desc_member_schema.json')
            )
        );
        $result = $mapper->result();
        /** @var SourceDescriptionEntity $row */
        $row = $result->getKsql()[0];
        $this->assertInstanceOf(SourceDescriptionEntity::class, $row);
        /** @var FieldInfo[] $fields */
        $fields = $row->getSourceDescription()->getFields();
        $this->assertIsArray($fields);
        $this->assertCount(4, $fields);
        /** @var FieldInfo $field */
        $field = $fields[3];
        $schemaFields = $field->getSchema()->getFieldInfo();
        $this->assertIsArray($schemaFields);
        $this->assertCount(4, $schemaFields);
        /** @var FieldInfo $schemaField */
        foreach ($schemaFields as $schemaField) {
            $this->assertInstanceOf(SchemaInfo::class, $schemaField->getSchema());
        }
    }
}
