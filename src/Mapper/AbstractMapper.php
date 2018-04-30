<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Psr\Http\Message\ResponseInterface;
use Istyle\KsqlClient\Entity\EntityInterface;

/**
 * should use extends
 * Class AbstractMapper
 */
abstract class AbstractMapper
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
