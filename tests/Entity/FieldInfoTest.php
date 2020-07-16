<?php
declare(strict_types=1);

use Ytake\KsqlClient\Entity\FieldInfo;
use Ytake\KsqlClient\Entity\SchemaInfo;

/**
 * Class FieldInfoTest
 */
final class FieldInfoTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldBeExpectedInstance(): void
    {
        $info = new FieldInfo('ROWTIME', null);
        $this->assertInstanceOf(\Ytake\KsqlClient\Entity\EntityInterface::class, $info);
    }

    public function testShouldReturnString(): void
    {
        $info = new FieldInfo('ROWTIME', null);
        $this->assertSame("FieldInfo{name='ROWTIME',schema=}", strval($info));
        $this->assertSame('ROWTIME', $info->getName());
        $this->assertNull($info->getSchema());
        $info = new FieldInfo('ROWTIME', new SchemaInfo('BIGINT', null, null));
        $this->assertNotSame("FieldInfo{name='ROWTIME',schema=}", $info);
        $this->assertSame('ROWTIME', $info->getName());
        $this->assertInstanceOf(SchemaInfo::class, $info->getSchema());
    }
}
