<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class KsqlCollection
 */
class KsqlCollection implements EntityInterface
{
    /** @var AbstractKsql[] */
    protected $ksql = [];

    /**
     * @param AbstractKsql $ksql
     */
    public function addKsql(AbstractKsql $ksql): void
    {
        $this->ksql[] = $ksql;
    }

    /**
     * @return AbstractKsql[]
     */
    public function getKsql(): array
    {
        return $this->ksql;
    }
}