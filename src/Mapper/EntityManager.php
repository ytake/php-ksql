<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Exception\UnknownJsonObjectsException;

use function array_key_exists;

/**
 * Class EntityManager
 */
class EntityManager
{
    /** @var array */
    private $row = [];

    /** @var ResultInterface[] */
    private $map = [
        'kafka_topics'      => KafkaTopicMapper::class,
        'streams'           => StreamsListMapper::class,
        'generic_error'     => KsqlErrorMapper::class,
        'statement_error'   => KsqlStatementErrorMapper::class,
        'tables'            => TablesListMapper::class,
        'queries'           => QueriesMapper::class,
        'properties'        => PropertiesMapper::class,
        'sourceDescription' => SourceDescriptionMapper::class,
        'queryDescription'  => QueryDescriptionMapper::class,
    ];

    /**
     * @param array $row
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    /**
     * @return KsqlEntity
     */
    public function map(): KsqlEntity
    {
        $type = $this->row['@type'] ?? '';
        if (array_key_exists($type, $this->map)) {
            /** @var ResultInterface $mapper */
            $mapper = new $this->map[$type]();
            return $mapper->result($this->row);
        }
        throw new UnknownJsonObjectsException('Unknown json objects.');
    }
}
