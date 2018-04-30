<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class AbstractKsql
 */
abstract class AbstractKsql
{
    /** @var string */
    protected $statementText;

    /**
     * @param string $statementText
     */
    public function __construct(string $statementText)
    {
        $this->statementText = $statementText;
    }

    /**
     * @return string
     */
    public function getStatementText(): string
    {
        return $this->statementText;
    }
}
