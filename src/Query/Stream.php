<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Mapper\StreamMapper;

/**
 * Class StreamQuery
 */
final class Stream extends AbstractStreamQuery
{
    /**
     * {@inheritdoc}
     */
    public function httpMethod(): string
    {
        return RequestMethodInterface::METHOD_POST;
    }

    /**
     * {@inheritdoc}
     */
    public function uri(): string
    {
        return 'query';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'ksql' => $this->query,
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return AbstractMapper
     */
    public function queryResult(ResponseInterface $response): AbstractMapper
    {
        $stream = new StreamMapper($response);
        $stream->setCallback($this->callback);

        return $stream;
    }
}
