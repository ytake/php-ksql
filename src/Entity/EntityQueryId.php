<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

use Istyle\KsqlClient\Query\QueryId;

/**
 * Class EntityQueryId
 */
final class EntityQueryId
{
    /** @var QueryId */
    private $id;

    /**
     * @param QueryId $id
     */
    public function __construct(QueryId $id)
    {
        $this->id = $id->getId();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
