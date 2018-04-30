<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\CommandStatus;
use Istyle\KsqlClient\Entity\EntityInterface;

/**
 * Class StatusCommandResult
 */
class CommandStatusMapper extends AbstractMapper
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
