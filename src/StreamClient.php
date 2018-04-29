<?php
declare(strict_types=1);

namespace Ytake\KsqlClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Ytake\KsqlClient\Exception\StreamQueryException;
use Ytake\KsqlClient\Query\AbstractStreamQuery;
use Ytake\KsqlClient\Query\QueryInterface;
use Ytake\KsqlClient\Result\AbstractResult;

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
                'User-Agent' => self::USER_AGENT,
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
     * @return AbstractResult
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestQuery(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): AbstractResult {
        if ($query instanceof AbstractStreamQuery) {
            return parent::requestQuery($query, $timeout, $debug);
        }
        throw new StreamQueryException(
            "You must extends " . AbstractStreamQuery::class
        );
    }
}
