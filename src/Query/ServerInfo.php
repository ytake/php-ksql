<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Istyle\KsqlClient\Mapper\ServerInfoMapper;

/**
 * Class ServerInfo
 */
final class ServerInfo implements QueryInterface
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
        return 'info';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function queryResult(ResponseInterface $response)
    {
        return new ServerInfoMapper($response);
    }
}
