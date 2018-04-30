<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Query;

use Istyle\KsqlClient\StreamConsumable;

/**
 * Class AbstractStreamQuery
 */
abstract class AbstractStreamQuery implements StreamQueryInterface
{
    /** @var string */
    protected $query;

    /** @var StreamConsumable */
    protected $callback;

    /**
     * StreamQuery constructor.
     *
     * @param string           $query
     * @param StreamConsumable $callback
     */
    public function __construct(string $query, StreamConsumable $callback)
    {
        $this->query = $query;
        $this->callback = $callback;
    }
}
