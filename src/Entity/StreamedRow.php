<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

use Ytake\KsqlClient\GenericRow;

/**
 * Class StreamedRow
 */
class StreamedRow implements EntityInterface
{
    /** @var array */
    protected $rows = [];

    /**
     * StreamedRow constructor.
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return GenericRow
     */
    public function getRow(): GenericRow
    {
        return new GenericRow($this->rows);
    }
}
