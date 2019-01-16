<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\KsqlEntity;
use Istyle\KsqlClient\Entity\QueryDescription;
use Istyle\KsqlClient\Entity\QueryDescriptionEntity;
use Istyle\KsqlClient\Entity\EntityQueryId;
use Istyle\KsqlClient\Query\QueryId;

/**
 * Class QueryDescriptionMapper
 */
final class QueryDescriptionMapper implements ResultInterface
{
    use RecursiveFieldTrait;

    /**
     * @param array $rows
     *
     * @return KsqlEntity
     */
    public function result(array $rows): KsqlEntity
    {
        $description = $rows['queryDescription'];
        $queryDescription = new QueryDescription(
            new EntityQueryId(new QueryId($description['id'])),
            $description['statementText'],
            $this->parentFields($description['fields'] ?? []),
            $description['sources'],
            $description['sinks'],
            $description['topology'],
            $description['executionPlan'],
            $description['overriddenProperties']
        );
        return new QueryDescriptionEntity(
            $rows['statementText'],
            $queryDescription
        );
    }
}
