<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class RunningQuery
 */
class RunningQuery implements EntityInterface
{
    /** @var string */
    protected $queryString;

    /** @var array<string> */
    protected $sinks = [];

    /** @var string */
    protected $id;

    /**
     * RunningQuery constructor.
     *
     * @param string $queryString
     * @param array  $sinks
     * @param string $id
     */
    public function __construct(
        string $queryString,
        array $sinks,
        string $id
    ) {
        $this->queryString = $queryString;
        $this->sinks = $sinks;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * @return array
     */
    public function getSinks(): array
    {
        return $this->sinks;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
