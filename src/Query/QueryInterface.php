<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Query;

use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Result\AbstractResult;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    /**
     * @return string
     */
    public function httpMethod(): string;

    /**
     * request uri for ksql-server
     *
     * @return string
     */
    public function uri(): string;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param ResponseInterface $response
     *
     * @return AbstractResult
     */
    public function queryResult(ResponseInterface $response);
}
