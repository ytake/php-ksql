<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

use Psr\Http\Message\ResponseInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;

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
     * @return AbstractMapper
     */
    public function queryResult(ResponseInterface $response);
}
