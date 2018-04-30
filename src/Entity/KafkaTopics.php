<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class KafkaTopics
 */
class KafkaTopics extends AbstractKsql
{
    /** @var array */
    private $kafkaTopicInfoList;

    /**
     * KafkaTopics constructor.
     *
     * @param string           $statementText
     * @param KafkaTopicInfo[] $kafkaTopicInfoList
     */
    public function __construct(
        string $statementText,
        array $kafkaTopicInfoList
    ) {
        parent::__construct($statementText);
        $this->kafkaTopicInfoList = $kafkaTopicInfoList;
    }

    /**
     * @return KafkaTopicInfo[]
     */
    public function getKafkaTopicInfoList(): array
    {
        return $this->kafkaTopicInfoList;
    }
}
