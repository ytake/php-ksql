<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\KafkaTopicInfo;
use Istyle\KsqlClient\Entity\KafkaTopics;

/**
 * Class KafkaTopicMapper
 */
class KafkaTopicMapper implements ResultInterface
{
    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $topics = [];
        foreach ($rows['topics'] as $topic) {
            $topics[] = new KafkaTopicInfo(
                $topic['name'],
                $topic['registered'],
                (is_array($topic['replicaInfo'])) ? $topic['replicaInfo'] : [$topic['replicaInfo']],
                $topic['consumerCount'],
                $topic['consumerGroupCount']
            );
        }
        return new KafkaTopics($rows['statementText'], $topics);
    }
}
