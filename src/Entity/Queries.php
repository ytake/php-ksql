<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class Queries
 */
final class Queries extends AbstractKsql
{
    /** @var RunningQuery[] */
    private $queries;

    /**
     * @param string $statementText
     * @param array  $queries
     */
    public function __construct(string $statementText, array $queries)
    {
        parent::__construct($statementText);
        $this->queries = $queries;
    }

    /**
     * @return RunningQuery[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
