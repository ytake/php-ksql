<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class KsqlErrorMessage
 */
class KsqlErrorMessage implements EntityInterface
{
    /** @var string */
    protected $message;

    /** @var array */
    protected $stackTrace;

    /**
     * @param string $message
     * @param array  $stackTrace
     */
    public function __construct(string $message, array $stackTrace)
    {
        $this->message = $message;
        $this->stackTrace = $stackTrace;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
    
    /**
     * @return array
     */
    public function getStackTrace(): array
    {
        return $this->stackTrace;
    }
}
