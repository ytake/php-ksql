<?php
declare(strict_types=1);

use Istyle\KsqlClient\Entity\FieldInfo;
use Istyle\KsqlClient\Entity\SchemaInfo;

/**
 * Class FieldInfoTest
 */
final class FieldInfoTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldBeExpectedInstance(): void
    {
        $info = new FieldInfo('ROWTIME', null);
        $this->assertInstanceOf(\Istyle\KsqlClient\Entity\EntityInterface::class, $info);
    }

    public function testShouldReturnString(): void
    {
        $info = new FieldInfo('ROWTIME', null);
        $this->assertSame("FieldInfo{name='ROWTIME',schema=}", strval($info));
        $info = new FieldInfo('ROWTIME', new SchemaInfo('BIGINT', null, null));
        $this->assertNotSame("FieldInfo{name='ROWTIME',schema=}", $info);
    }
}
