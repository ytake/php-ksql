<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class TablesList
 */
final class TablesList extends KsqlEntity
{
    /** @var SourceInfoTable[] */
    private $sourceInfoTable;

    /**
     * @param string $statementText
     * @param SourceInfoTable[]  $sourceInfoTable
     */
    public function __construct(
        string $statementText,
        array $sourceInfoTable
    ) {
        parent::__construct($statementText);
        $this->sourceInfoTable = $sourceInfoTable;
    }

    /**
     * @return SourceInfoTable[]
     */
    public function getSourceInfoList(): array
    {
        return $this->sourceInfoTable;
    }
}
