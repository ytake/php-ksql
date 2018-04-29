<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Result\AbstractResult;
use Ytake\KsqlClient\Result\StatusResult;

/**
 * Class Status
 */
class Status implements QueryInterface
{
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
        return 'status';
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
        return new StatusResult($response);
    }
}
