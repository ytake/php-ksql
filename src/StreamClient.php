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

namespace Istyle\KsqlClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Istyle\KsqlClient\Exception\StreamQueryException;
use Istyle\KsqlClient\Query\AbstractStreamQuery;
use Istyle\KsqlClient\Query\QueryInterface;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Stream\QueryStream;
use Psr\Http\Message\ResponseInterface;

/**
 * Class StreamClient
 */
class StreamClient extends RestClient
{
    /**
     * {@inheritdoc}
     */
    protected function buildClient(): ClientInterface
    {
        return new GuzzleClient([
            $this->requestHeader(),
            RequestOptions::STREAM => true,
        ]);
    }

    /**
     * @param AbstractStreamQuery $query
     *
     * @return QueryStream
     */
    protected function sinkStream(AbstractStreamQuery $query): QueryStream
    {
        return new QueryStream($query);
    }

    /**
     * {@inheritdoc}
     */
    public function requestQuery(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): AbstractMapper {
        if ($query instanceof AbstractStreamQuery) {
            $stream = $this->sinkStream($query);
            $this->setOptions([
                RequestOptions::SINK => $stream,
            ]);
            return parent::requestQuery($query, $timeout, $debug);
        }
        throw new StreamQueryException(
            "You must extends " . AbstractStreamQuery::class
        );
    }
}
