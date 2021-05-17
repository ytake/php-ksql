<?php

declare(strict_types=1);

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Ytake\KsqlClient\Mapper;

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Utils;
use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Entity\KsqlErrorMessage;
use Ytake\KsqlClient\Entity\StreamedRow;
use Ytake\KsqlClient\Entity\StreamedRows;
use Ytake\KsqlClient\StreamConsumable;

use function call_user_func_array;
use function strval;
use function trim;

/**
 * Class StreamResult
 */
final class StreamMapper extends AbstractMapper
{
    /** @var StreamConsumable */
    private StreamConsumable $callback;

    /**
     * @param StreamConsumable $callback
     */
    public function setCallback(
        StreamConsumable $callback
    ): void {
        $this->callback = $callback;
    }

    /**
     * @return EntityInterface
     */
    public function result(): EntityInterface
    {
        $stream = $this->response->getBody();
        $streamed = [];
        if ($stream instanceof Stream) {
            while (!$stream->eof()) {
                $line = trim(\GuzzleHttp\Psr7\Utils::readLine($stream));
                if (!empty($line)) {
                    $decode = Utils::jsonDecode($line, true);
                    $row = new StreamedRow(
                        $decode['row']['columns'],
                        new KsqlErrorMessage(
                            0,
                            strval($decode['errorMessage'])
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
