<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\KsqlErrorMessage;

/**
 * Class ErrorResult
 */
class ErrorMapper extends AbstractMapper
{
    /**
     * @return EntityInterface|KsqlErrorMessage
     */
    public function result(): EntityInterface
    {
        $decode = \GuzzleHttp\json_decode(
            $this->response->getBody()->getContents(), true
        );
        return new KsqlErrorMessage(
            $decode['message'] ?? '',
            $decode['stackTrace'] ?? []
        );
    }
}
