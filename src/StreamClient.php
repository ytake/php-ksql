<?php
declare(strict_types=1);

namespace Istyle\KsqlClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Istyle\KsqlClient\Exception\StreamQueryException;
use Istyle\KsqlClient\Query\AbstractStreamQuery;
use Istyle\KsqlClient\Query\QueryInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;

/**
 * Class StreamClient
 */
final class StreamClient extends RestClient
{
    /**
     * build GuzzleHttp Client
     *
     * @return ClientInterface
     */
    protected function buildClient(): ClientInterface
    {
        return new GuzzleClient([
            'headers' => [
                'User-Agent' => $this->userAgent(),
                'Accept'     => 'application/json',
            ],
            'stream'  => true,
        ]);
    }

    /**
     * @param QueryInterface $query
     * @param int            $timeout
     * @param bool           $debug
     *
     * @return AbstractMapper
     */
    public function requestQuery(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): AbstractMapper {
        if ($query instanceof AbstractStreamQuery) {
            return parent::requestQuery($query, $timeout, $debug);
        }
        throw new StreamQueryException(
            "You must extends " . AbstractStreamQuery::class
        );
    }
}
