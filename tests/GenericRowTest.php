<?php
declare(strict_types=1);

use Istyle\KsqlClient\GenericRow;

class GenericRowTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldBe(): void
    {
        $row = new GenericRow(['']);
        $this->assertArrayHasKey(0, $row->getColumns());
        $this->assertNull($row->getColumnValue(2));
    }
}
