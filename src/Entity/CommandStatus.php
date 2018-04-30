<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class CommandStatus
 */
class CommandStatus implements EntityInterface
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $status;

    /**
     * @param string $message
     * @param string $status
     */
    public function __construct(string $message, string $status)
    {
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
