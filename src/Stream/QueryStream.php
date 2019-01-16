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

namespace Istyle\KsqlClient\Stream;

use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Response;
use Istyle\KsqlClient\Query\AbstractStreamQuery;

/**
 * Class QueryStream
 */
class QueryStream extends BufferStream
{
    /** @var AbstractStreamQuery */
    private $callback;

    /**
     * @param AbstractStreamQuery $callback
     * @param int                 $hwm
     */
    public function __construct(AbstractStreamQuery $callback, int $hwm = 16384)
    {
        parent::__construct($hwm);
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $line = trim($string, "\r\n");
        if (!empty($line)) {
            $mapper = $this->callback->queryResult(new Response(200, [], $line));
            $mapper->result();
        }
        return parent::write($string);
    }
}
