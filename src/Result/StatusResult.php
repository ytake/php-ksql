<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use Ytake\KsqlClient\Entity\CommandStatus;
use Ytake\KsqlClient\Entity\CommandStatuses;
use Ytake\KsqlClient\Entity\EntityInterface;

/**
 * Class StatusResult
 */
class StatusResult extends AbstractResult
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
        /**
         * response
         * {
         *   "commandStatuses": [
         *     {"stream/EXAMPLE/create":"SUCCESS"}
         *   ]
         * }
         */
        foreach ($decode['commandStatuses'] as $commandId => $status) {
            $statuses->addCommandStatus(new CommandStatus($commandId, $status));
        }

        return $statuses;
    }
}
