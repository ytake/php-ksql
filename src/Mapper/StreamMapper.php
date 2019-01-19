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
final class StreamMapper extends AbstractMapper
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
