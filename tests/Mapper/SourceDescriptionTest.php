<?php
declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Ytake\KsqlClient\Mapper\KsqlMapper;
use Ytake\KsqlClient\Entity\FieldInfo;
use Ytake\KsqlClient\Entity\SourceDescriptionEntity;
use Ytake\KsqlClient\Entity\SchemaInfo;

final class SourceDescriptionTest extends \PHPUnit\Framework\TestCase
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
