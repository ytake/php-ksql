<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Entity\SourceInfo;
use Istyle\KsqlClient\Entity\StreamsList;

/**
 * Class StreamsListMapper
 */
class StreamsListMapper implements ResultInterface
{
    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    public function result(array $row): AbstractKsql
    {
        $streams = [];
        foreach ($row['streams'] as $stream) {
            $streams[] = new SourceInfo(
                $stream['type'],
                $stream['name'],
                $stream['topic'],
                $stream['format']
            );
        }
        return new StreamsList($row['statementText'], $streams);
    }
}
