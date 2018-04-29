<?php
declare(strict_types=1);

namespace Ytake\KsqlClient;

use Ytake\KsqlClient\Entity\StreamedRow;

/**
 * Interface StreamConsumable
 */
interface StreamConsumable
{
    /**
     * @param StreamedRow $row
     *
     * @return mixed
     */
    public function __invoke(StreamedRow $row);
}
