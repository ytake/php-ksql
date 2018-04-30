<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ResponseInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Mapper\KsqlMapper;

/**
 * Class Ksql
 */
final class Ksql implements QueryInterface
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
     * @return AbstractMapper
     */
    public function queryResult(ResponseInterface $response): AbstractMapper
    {
        return new KsqlMapper($response);
    }
}
