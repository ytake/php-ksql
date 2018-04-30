<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Entity;

use Istyle\KsqlClient\GenericRow;

/**
 * Class StreamedRow
 */
final class StreamedRow implements EntityInterface
{
    /** @var array */
    private $rows = [];

    /** @var KsqlErrorMessage */
    private $ksqlErrorMessage;

    /**
     * @param array            $rows
     * @param KsqlErrorMessage $ksqlErrorMessage
     */
    public function __construct(array $rows, KsqlErrorMessage $ksqlErrorMessage)
    {
        $this->rows = $rows;
        $this->ksqlErrorMessage = $ksqlErrorMessage;
    }

    /**
     * @return GenericRow
     */
    public function getRow(): GenericRow
    {
        return new GenericRow($this->rows);
    }

    /**
     * @return KsqlErrorMessage
     */
    public function getKsqlErrorMessage(): KsqlErrorMessage
    {
        return $this->ksqlErrorMessage;
    }
}
