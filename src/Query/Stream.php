<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Result\AbstractResult;
use Ytake\KsqlClient\Result\StreamResult;

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
     * @return AbstractResult
     */
    public function queryResult(ResponseInterface $response): AbstractResult
    {
        $stream = new StreamResult($response);
        $stream->setCallback($this->callback);

        return $stream;
    }
}
