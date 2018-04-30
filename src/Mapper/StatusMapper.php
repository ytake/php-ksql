<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\CommandStatus;
use Istyle\KsqlClient\Entity\CommandStatuses;
use Istyle\KsqlClient\Entity\EntityInterface;

/**
 * Class StatusResult
 */
final class StatusMapper extends AbstractMapper
{
    /**
     * @return EntityInterface|CommandStatuses
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );
        $statuses = new CommandStatuses();
        foreach ($decode['commandStatuses'] as $commandId => $status) {
            $statuses->addCommandStatus(new CommandStatus($commandId, $status));
        }

        return $statuses;
    }
}
