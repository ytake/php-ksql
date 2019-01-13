<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\AbstractKsql;
use Istyle\KsqlClient\Entity\KsqlStatementErrorMessage;

final class KsqlStatementErrorMapper implements ResultInterface
{
    /**
     * @param array $row
     *
     * @return AbstractKsql
     */
    public function result(array $row): AbstractKsql
    {
        return new KsqlStatementErrorMessage(
            $row['statementText'],
            $row['error_code'],
            $row['message'],
            $row['stackTrace'],
            $row['entities']
        );
    }
}
