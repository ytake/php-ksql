<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class KafkaTopicInfo
 */
class KafkaTopicInfo implements EntityInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $registered;

    /** @var array */
    private $replicaInfo;

    /** @var int */
    private $consumerCount;

    /** @var int */
    private $consumerGroupCount;

    /**
     * @param string $name
     * @param string $registered
     * @param array  $replicaInfo
     * @param int    $consumerCount
     * @param int    $consumerGroupCount
     */
    public function __construct(
        string $name,
        string $registered,
        array $replicaInfo,
        int $consumerCount,
        int $consumerGroupCount
    ) {
        $this->name = $name;
        $this->registered = $registered;
        $this->replicaInfo = $replicaInfo;
        $this->consumerCount = $consumerCount;
        $this->consumerGroupCount = $consumerGroupCount;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRegistered(): string
    {
        return $this->registered;
    }

    /**
     * @return array
     */
    public function getReplicaInfo(): array
    {
        return $this->replicaInfo;
    }

    /**
     * @return int
     */
    public function getConsumerCount(): int
    {
        return $this->consumerCount;
    }

    /**
     * @return int
     */
    public function getConsumerGroupCount(): int
    {
        return $this->consumerGroupCount;
    }
}
