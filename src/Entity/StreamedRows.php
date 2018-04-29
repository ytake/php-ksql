<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class StreamedRow
 */
class StreamedRows implements EntityInterface
{
    /** @var StreamedRow[] */
    protected $rows = [];

    /**
     * @param StreamedRow[] $rows
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return StreamedRow[]
     */
    public function getRow(): array
    {
        return $this->rows;
    }
}
