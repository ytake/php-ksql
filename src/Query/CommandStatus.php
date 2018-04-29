<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Result\AbstractResult;
use Ytake\KsqlClient\Result\CommandStatusResult;

/**
 * Class StatusCommand
 */
class CommandStatus implements QueryInterface
{
    /** @var string */
    protected $commandId;

    /**
     * @param string $commandId
     */
    public function __construct(string $commandId)
    {
        $this->commandId = $commandId;
    }

    /**
     * {@inheritdoc}
     */
    public function httpMethod(): string
    {
        return RequestMethodInterface::METHOD_GET;
    }

    /**
     * {@inheritdoc}
     */
    public function uri(): string
    {
        return sprintf('status/%s', $this->commandId);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return AbstractResult
     */
    public function queryResult(ResponseInterface $response): AbstractResult
    {
        return new CommandStatusResult($response);
    }
}
