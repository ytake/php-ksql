<?php

declare(strict_types=1);

namespace Tests\Query;

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Query\QueryId;

use function strval;

final class QueryIdTest extends TestCase
{
    public function testShouldReturnQueryIdString(): void
    {
        $id = new QueryId('123456789');
        $this->assertSame('123456789', $id->getId());
        $this->assertSame(strval($id), $id->getId());
    }
}
