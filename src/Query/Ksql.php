<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Result\AbstractResult;
use Ytake\KsqlClient\Result\KsqlResult;

/**
 * Class Ksql
 */
class Ksql implements QueryInterface
{
    /** @var string */
    protected $query;

    /**
     * Ksql constructor.
     *
     * @param string $query
     */
    public function __construct(string $query)
    {
        $this->query = $query;
    }

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
        return 'ksql';
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
        return new KsqlResult($response);
    }
}
