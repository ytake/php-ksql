<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

/**
 * Class QueryId
 */
final class QueryId
{
    /** @var string */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
