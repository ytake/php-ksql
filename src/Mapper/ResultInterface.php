<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;

interface ResultInterface
{
    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    public function result(array $row): AbstractKsql;
}
