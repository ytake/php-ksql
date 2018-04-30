<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class CommandStatuses
 */
final class CommandStatuses implements EntityInterface
{
    /** @var CommandStatus[] */
    private $statuses = [];

    /**
     * @param CommandStatus $status
     */
    public function addCommandStatus(CommandStatus $status): void
    {
        $this->statuses[] = $status;
    }

    /**
     * @return CommandStatus[]
     */
    public function fullStatuses(): array
    {
        return $this->statuses;
    }
}
