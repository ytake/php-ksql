<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Computation;

use InvalidArgumentException;
use function explode;

/**
 * Class CommandId
 */
final class CommandId
{
    /** @var string */
    private $type;

    /** @var string */
    private $entity;

    /** @var string */
    private $action;

    /**
     * @param string $type
     * @param string $entity
     * @param string $action
     */
    public function __construct(
        string $type,
        string $entity,
        string $action
    ) {
        $this->type = $type;
        $this->entity = $entity;
        $this->action = $action;
    }

    /**
     * @param string $fromString
     *
     * @return CommandId
     */
    public static function fromString(string $fromString): CommandId
    {
        $split = explode('/', $fromString);
        if (count($split) != 3) {
            throw new InvalidArgumentException("Expected a string of the form <type>/<entity>/<action>");
        }

        return new CommandId($split[0], $split[1], $split[2]);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
