<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Entity\KafkaTopicInfo;
use Istyle\KsqlClient\Entity\KafkaTopics;

/**
 * Class KafkaTopicMapper
 */
class KafkaTopicMapper implements ResultInterface
{
    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    public function result(array $row): AbstractKsql
    {
        $topics = [];
        foreach ($row['topics'] as $topic) {
            $topics[] = new KafkaTopicInfo(
                $topic['name'],
                $topic['registered'],
                (is_array($topic['replicaInfo'])) ? $topic['replicaInfo'] : [$topic['replicaInfo']],
                $topic['consumerCount'],
                $topic['consumerGroupCount']
            );
        }
        return new KafkaTopics($row['statementText'], $topics);
    }
}
