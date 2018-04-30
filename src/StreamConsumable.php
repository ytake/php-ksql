<?php
declare(strict_types=1);

namespace Istyle\KsqlClient;

use Istyle\KsqlClient\Entity\StreamedRow;

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
