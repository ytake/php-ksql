<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Istyle\KsqlClient\Computation\CommandId;

/**
 * Class ComputationCommandIdTest
 */
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowException(): void
    {
        CommandId::fromString('');
    }
}
