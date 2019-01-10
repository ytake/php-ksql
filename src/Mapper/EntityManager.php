<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Exception\UnknownJsonObjectsException;

use function array_key_exists;

/**
 * Class EntityManager
 */
class EntityManager
{
    /** @var array */
    private $row = [];

    /** @var ResultInterface[]
     */
    private $map = [
        'kafka_topics' => KafkaTopicMapper::class,
        'streams'      => StreamsListMapper::class,
    ];

    /**
     * @param array $row
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    /**
     * @return AbstractKsql
     */
    public function map(): AbstractKsql
    {
        $type = $this->row['@type'] ?? '';
        if ($type === '') {
            throw new UnknownJsonObjectsException('Unknown json objects.');
        }
        if (array_key_exists($type, $this->map)) {
            /** @var ResultInterface $mapper */
            $mapper = new $this->map[$type]();
            return $mapper->result($this->row);
        }
    }
}
