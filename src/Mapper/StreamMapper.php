<?php
declare(strict_types=1);

namespace Istyle\KsqlClient\Mapper;

use GuzzleHttp\Psr7\Stream;
use Istyle\KsqlClient\Entity\EntityInterface;
use Istyle\KsqlClient\Entity\KsqlErrorMessage;
use Istyle\KsqlClient\Entity\StreamedRow;
use Istyle\KsqlClient\Entity\StreamedRows;
use Istyle\KsqlClient\StreamConsumable;

/**
 * Class StreamResult
 */
class StreamMapper extends AbstractMapper
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
                    $errorMessage = $decode['errorMessage'];
                    $row = new StreamedRow(
                        $decode['row']['columns'],
                        new KsqlErrorMessage(
                            $errorMessage['message'] ?? '',
                            $errorMessage['stackTrace'] ?? []
                        )
                    );
                    call_user_func_array($this->callback, [$row]);
                    $streamed[] = $row;
                }
            }
        }

        return new StreamedRows($streamed);
    }
}
