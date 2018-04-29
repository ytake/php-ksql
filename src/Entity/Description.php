<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class Description
 */
final class Description extends AbstractKsql
{
    /** @var string */
    private $name;

    /** @var RunningQuery[] */
    private $readQueries;

    /** @var RunningQuery[] */
    private $writeQueries;

    /** @var FieldSchema */
    private $schema;

    /** @var string */
    private $type;

    /** @var string */
    private $key;

    /** @var string */
    private $timestamp;

    /** @var string */
    private $statistics;

    /** @var string */
    private $errorStats;

    /** @var bool */
    private $extended;

    /** @var int */
    private $partitions;

    /** @var int */
    private $replication;

    /**
     * @param string $statementText
     * @param string $name
     * @param array  $readQueries
     * @param array  $writeQueries
     * @param array  $schema
     * @param string $type
     * @param string $key
     * @param string $timestamp
     * @param string $statistics
     * @param string $errorStats
     * @param bool   $extended
     * @param int    $partitions
     * @param int    $replication
     */
    public function __construct(
        string $statementText,
        string $name,
        array $readQueries,
        array $writeQueries,
        array $schema,
        string $type,
        string $key,
        string $timestamp,
        string $statistics,
        string $errorStats,
        bool $extended,
        int $partitions,
        int $replication
    ) {
        parent::__construct($statementText);
        $this->name = $name;
        $this->readQueries = $readQueries;
        $this->writeQueries = $writeQueries;
        $this->schema = $schema;
        $this->type = $type;
        $this->key = $key;
        $this->timestamp = $timestamp;
        $this->statistics = $statistics;
        $this->errorStats = $errorStats;
        $this->extended = $extended;
        $this->partitions = $partitions;
        $this->replication = $replication;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return RunningQuery[]
     */
    public function getReadQueries(): array
    {
        return $this->readQueries;
    }

    /**
     * @return RunningQuery[]
     */
    public function getWriteQueries(): array
    {
        return $this->writeQueries;
    }

    /**
     * @return FieldSchema
     */
    public function getSchema(): FieldSchema
    {
        return $this->schema;
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
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getStatistics(): string
    {
        return $this->statistics;
    }

    /**
     * @return string
     */
    public function getErrorStats(): string
    {
        return $this->errorStats;
    }

    /**
     * @return bool
     */
    public function isExtended(): bool
    {
        return $this->extended;
    }

    /**
     * @return int
     */
    public function getPartitions(): int
    {
        return $this->partitions;
    }

    /**
     * @return int
     */
    public function getReplication(): int
    {
        return $this->replication;
    }
}
