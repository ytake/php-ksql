<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use Psr\Http\Message\ResponseInterface;
use Ytake\KsqlClient\Entity\EntityInterface;

/**
 * Class AbstractResult
 */
abstract class AbstractResult
{
    /** @var ResponseInterface */
    protected $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * return Result Entity
     * @return EntityInterface
     */
    abstract public function result(): EntityInterface;
}
