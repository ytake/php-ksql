<?php
declare(strict_types=1);

namespace Ytake\KsqlClient\Result;

use GuzzleHttp\Psr7\Stream;
use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Entity\StreamedRow;
use Ytake\KsqlClient\Entity\StreamedRows;
use Ytake\KsqlClient\StreamConsumable;

/**
 * Class StreamResult
 */
class StreamResult extends AbstractResult
{
    /** @var StreamConsumable */
    protected $callback;

    /**
     * @param StreamConsumable $callback
     */
    public function setCallback(StreamConsumable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @return EntityInterface|StreamedRows
     */
    public function result(): EntityInterface
    {
        $stream = $this->response->getBody();
        $streamed = [];
        if ($stream instanceof Stream) {
            while (!$stream->eof()) {
                $line = trim(\GuzzleHttp\Psr7\readline($stream));
                if (!empty($line)) {
                    $decode = \GuzzleHttp\json_decode($line, true);
                    $row = new StreamedRow($decode['row']);
                    call_user_func_array($this->callback, [$row]);
                    $streamed[] = $row;
                }
            }
        }

        return new StreamedRows($streamed);
    }
}
