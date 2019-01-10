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

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use Istyle\KsqlClient\Exception\KsqlRestClientException;
use Istyle\KsqlClient\Mapper\AbstractMapper;
use Istyle\KsqlClient\Mapper\ErrorMapper;
use Istyle\KsqlClient\Query\QueryInterface;

/**
 * Class RestClient
 */
class RestClient implements \Istyle\KsqlClient\ClientInterface
{
    const VERSION = '0.2.0';

    /** @var string */
    private $serverAddress;

    /** @var array<string, string> */
    private $properties = [];

    /** @var ClientInterface */
    private $client;

    /** @var bool */
    private $hasUserCredentials = false;

    /** @var AuthCredential */
    private $authCredential;

    /** @var array */
    private $options = [];

    /**
     * RestClient constructor.
     *
     * @param string               $serverAddress
     * @param array                $properties
     * @param ClientInterface|null $client
     */
    public function __construct(
        string $serverAddress,
        array $properties = [],
        ClientInterface $client = null
    ) {
        $this->serverAddress = $serverAddress;
        $this->properties = $properties;
        $this->client = (is_null($client)) ? $this->buildClient() : $client;
    }

    /**
     * build GuzzleHttp Client
     *
     * @return ClientInterface
     */
    protected function buildClient(): ClientInterface
    {
        return new GuzzleClient([
            $this->requestHeader()
        ]);
    }

    /**
     * @param AuthCredential $authCredential
     */
    public function setAuthCredentials(AuthCredential $authCredential): void
    {
        $this->authCredential = $authCredential;
        $this->hasUserCredentials = true;
    }

    /**
     * @return null|AuthCredential
     */
    public function getAuthCredentials(): ?AuthCredential
    {
        return $this->authCredential;
    }

    /**
     * @return string
     */
    public function getServerAddress(): string
    {
        return $this->serverAddress;
    }

    /**
     * @param string $serverAddress
     */
    public function setServerAddress(string $serverAddress): void
    {
        $this->serverAddress = $serverAddress;
    }

    /**
     * @param QueryInterface $query
     * @param int            $timeout
     * @param bool           $debug
     *
     * @return AbstractMapper
     */
    public function requestQuery(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): AbstractMapper {
        $uri = new Uri($this->serverAddress);
        $uri = $uri->withPath($query->uri());
        $normalize = UriNormalizer::normalize(
            $uri,
            UriNormalizer::REMOVE_DUPLICATE_SLASHES
        );
        $request = new Request($query->httpMethod(), $normalize);
        try {
            $options = $this->getOptions($query, $timeout, $debug);
            if ($this->hasUserCredentials) {
                $credentials = $this->getAuthCredentials();
                $options = array_merge($options, [
                    'auth' => [$credentials->getUserName(), $credentials->getPassword()],
                ]);
            }
            $response = $this->client->send(
                $request,
                array_merge($options, $this->options)
            );
            if ($response->getStatusCode() == StatusCodeInterface::STATUS_OK) {
                return $query->queryResult($response);
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new KsqlRestClientException($e->getMessage(), $e->getCode());
        }

        return new ErrorMapper($response);
    }

    /**
     * @param QueryInterface $query
     * @param int            $timeout
     * @param bool           $debug
     *
     * @return array
     */
    protected function getOptions(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): array {
        return [
            'timeout' => $timeout,
            'body'    => json_encode($query->toArray()),
            'debug'   => $debug,
        ];
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * user agent
     * @return string
     */
    protected function userAgent(): string
    {
        return 'PHP-KSQLClient/' . self::VERSION;
    }

    /**
     * @return array
     */
    protected function requestHeader(): array
    {
        return [
            'headers' => [
                'User-Agent' => $this->userAgent(),
                'Accept'     => \Istyle\KsqlClient\ClientInterface::RequestAccept,
            ],
        ];
    }
}
