<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use Ytake\KsqlClient\Entity\CommandStatus;
use Ytake\KsqlClient\Entity\EntityInterface;

/**
 * Class StatusCommandResult
 */
class CommandStatusResult extends AbstractResult
{
    /**
     * @return EntityInterface|CommandStatus
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );

        return new CommandStatus($decode['message'], $decode['status']);
    }
}
