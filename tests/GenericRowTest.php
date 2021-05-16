<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\GenericRow;

final class GenericRowTest extends TestCase
{
    public function testShouldBe(): void
    {
        $row = new GenericRow(['']);
        $this->assertArrayHasKey(0, $row->getColumns());
        $this->assertNull($row->getColumnValue(2));
    }
}
