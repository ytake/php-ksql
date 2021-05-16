<?php

declare(strict_types=1);

namespace Tests\Entity;

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Entity\KsqlErrorMessage;

use function strval;

final class KsqlErrorMessageTest extends TestCase
{
    public function testShouldBeErrorMessageTExt(): void
    {
        $message = new KsqlErrorMessage(100, 'testing', ['ksql', 'test']);
        $this->assertSame(
            strval($message),
            "testing
ksql
test"
        );
    }
}
