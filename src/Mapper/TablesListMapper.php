<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\SourceInfoTable;
use Istyle\KsqlClient\Entity\TablesList;

/**
 * Class TablesListMapper
 */
class TablesListMapper implements ResultInterface
{
    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $tables = [];
        foreach ($rows['tables'] as $row) {
            $tables[] = new SourceInfoTable(
                $row['name'],
                $row['topic'],
                $row['format'],
                $row['isWindowed']
            );
        }
        return new TablesList($rows['statementText'], $tables);
    }
}
