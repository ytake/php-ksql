<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Entity;

/**
 * Class KsqlStatementErrorMessage
 */
class KsqlStatementErrorMessage extends AbstractKsql
{
    /** @var KsqlErrorMessage */
    private $errorMessage;

    /**
     * @param string           $statementText
     * @param KsqlErrorMessage $errorMessage
     */
    public function __construct(string $statementText, KsqlErrorMessage $errorMessage)
    {
        parent::__construct($statementText);
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getStatementText(): string
    {
        return $this->statementText;
    }

    /**
     * @return KsqlErrorMessage
     */
    public function getErrorMessage(): KsqlErrorMessage
    {
        return $this->errorMessage;
    }
}
