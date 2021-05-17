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

namespace Ytake\KsqlClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Ytake\KsqlClient\Exception\StreamQueryException;
use Ytake\KsqlClient\Mapper\ResultInterface;
use Ytake\KsqlClient\Properties\LocalProperties;
use Ytake\KsqlClient\Query\AbstractStreamQuery;
use Ytake\KsqlClient\Query\QueryInterface;

use function array_merge;

/**
 * KSQL Stream API Client
 */
class StreamClient extends RestClient
{
    /**
     * {@inheritdoc}
     */
    protected function buildClient(): ClientInterface
    {
        return new GuzzleClient(
            array_merge(
                $this->requestHeader(),
                [
                    RequestOptions::STREAM => true,
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function requestQuery(
        QueryInterface $query,
        ?LocalProperties $streamsProperties = null,
        int $timeout = 500000,
        bool $debug = false
    ): ResultInterface {
        if ($query instanceof AbstractStreamQuery) {
            return parent::requestQuery($query, $streamsProperties, $timeout, $debug);
        }
        throw new StreamQueryException(
            "You must extends " . AbstractStreamQuery::class
        );
    }
}
