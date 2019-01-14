<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\SourceInfo;
use Istyle\KsqlClient\Entity\StreamsList;

/**
 * Class StreamsListMapper
 */
class StreamsListMapper implements ResultInterface
{
    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $streams = [];
        foreach ($rows['streams'] as $stream) {
            $streams[] = new SourceInfo(
                $stream['name'],
                $stream['topic'],
                $stream['format']
            );
        }
        return new StreamsList($rows['statementText'], $streams);
    }
}
