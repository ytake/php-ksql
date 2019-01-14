<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\KsqlStatementErrorMessage;

final class KsqlStatementErrorMapper implements ResultInterface
{
    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        return new KsqlStatementErrorMessage(
            $rows['statementText'],
            $rows['error_code'],
            $rows['message'],
            $rows['stackTrace'],
            $rows['entities']
        );
    }
}
