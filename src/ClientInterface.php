<?php
declare(strict_types=1);

namespace Istyle\KsqlClient;

use Istyle\KsqlClient\Query\QueryInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;

interface ClientInterface
{
    const RequestAccept = 'application/vnd.ksql.v1+json';

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
    ): AbstractMapper;
}
