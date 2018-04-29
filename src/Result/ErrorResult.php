<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Entity\KsqlErrorMessage;

/**
 * Class ErrorResult
 */
class ErrorResult extends AbstractResult
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
