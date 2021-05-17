<?php

declare(strict_types=1);

namespace Tests\Computation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Computation\CommandId;

final class ComputationCommandIdTest extends TestCase
{
    public function testShouldReturnCommandIdInstance(): void
    {
        $commandId = CommandId::fromString('stream/KSQLTESTING/create');
        $this->assertInstanceOf(CommandId::class, $commandId);
        $this->assertSame('create', $commandId->getAction());
        $this->assertSame('stream', $commandId->getType());
        $this->assertSame('KSQLTESTING', $commandId->getEntity());
        $this->assertSame('stream/KSQLTESTING/create', \strval($commandId));
    }

    public function testShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected a string of the form <type>/<entity>/<action>');
        CommandId::fromString('');
    }
}
