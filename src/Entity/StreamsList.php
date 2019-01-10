<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

/**
 * Class StreamsList
 */
final class StreamsList extends AbstractKsql
{
    /** @var array */
    private $sourceInfoList;

    /**
     * @param string $statementText
     * @param SourceInfo[]  $sourceInfoList
     */
    public function __construct(
        string $statementText,
        array $sourceInfoList
    ) {
        parent::__construct($statementText);
        $this->sourceInfoList = $sourceInfoList;
    }

    /**
     * @return SourceInfo[]
     */
    public function getSourceInfoList(): array
    {
        return $this->sourceInfoList;
    }
}
