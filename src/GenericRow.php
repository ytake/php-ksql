<?php
declare(strict_types=1);

namespace Ytake\KsqlClient;

/**
 * Class GenericRow
 */
final class GenericRow
{
    /** @var array */
    private $columns = [];

    /**
     * @param array $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param int $index
     *
     * @return array|mixed|null
     */
    public function getColumnValue(int $index)
    {
        return $this->columns[$index] ?? null;
    }
}
