<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Istyle\KsqlClient\Query\QueryId;

final class QueryIdTest extends TestCase
{
    public function testShouldReturnQueryIdString(): void
    {
        $id = new QueryId('123456789');
        $this->assertSame('123456789', $id->getId());
        $this->assertSame(strval($id), $id->getId());
    }
}
