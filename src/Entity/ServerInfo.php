<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class ServerInfo
 */
final class ServerInfo implements EntityInterface
{
    /** @var string */
    private $version;

    /** @var string */
    private $kafkaClusterId;

    /** @var string */
    private $ksqlServiceId;

    /**
     * @param string $version
     * @param string $kafkaClusterId
     * @param string $ksqlServiceId
     */
    public function __construct(
        string $version,
        string $kafkaClusterId,
        string $ksqlServiceId
    ) {
        $this->version = $version;
        $this->kafkaClusterId = $kafkaClusterId;
        $this->ksqlServiceId = $ksqlServiceId;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getKafkaClusterId(): string
    {
        return $this->kafkaClusterId;
    }

    /**
     * @return string
     */
    public function getKsqlServiceId(): string
    {
        return $this->ksqlServiceId;
    }
}
