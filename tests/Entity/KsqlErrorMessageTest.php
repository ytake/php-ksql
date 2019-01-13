<?php
declare(strict_types=1);

use Istyle\KsqlClient\Entity\KsqlErrorMessage;


final class KsqlErrorMessageTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldBeErrorMessageTExt(): void
    {
        $message = new KsqlErrorMessage(100, 'testing', ['ksql', 'test']);
        $this->assertSame(strval($message), "testing
ksql
test");
    }
}
