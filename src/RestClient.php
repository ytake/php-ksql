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

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use stdClass;
use Ytake\KsqlClient\Exception\KsqlRestClientException;
use Ytake\KsqlClient\Mapper\KsqlErrorMapper;
use Ytake\KsqlClient\Mapper\ResultInterface;
use Ytake\KsqlClient\Properties\LocalProperties;
use Ytake\KsqlClient\Query\QueryInterface;

use function array_merge;
use function is_null;
use function json_encode;

/**
 * KSQL REST API Client
 */
class RestClient implements \Ytake\KsqlClient\ClientInterface
{
    const VERSION = '3.0.0';

    /** @var ClientInterface */
    private ClientInterface $client;

    /** @var bool */
    private bool $hasUserCredentials = false;

    /** @var AuthCredential|null */
    private ?AuthCredential $authCredential = null;

    /** @var array */
    private array $options = [];

    /** @var LocalProperties|null */
    private ?LocalProperties $properties = null;

    /**
     * RestClient constructor.
     *
     * @param string $serverAddress
     * @param ClientInterface|null $client
     */
    public function __construct(
        private string $serverAddress,
        ClientInterface $client = null
    ) {
        $this->client = (is_null($client)) ? $this->buildClient() : $client;
    }

    /**
     * build GuzzleHttp Client
     *
     * @return ClientInterface
     */
    protected function buildClient(): ClientInterface
    {
        return new GuzzleClient(
            $this->requestHeader()
        );
    }

    /**
     * @param AuthCredential $authCredential
     */
    public function setAuthCredentials(
        AuthCredential $authCredential
    ): void {
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
    public function setServerAddress(
        string $serverAddress
    ): void {
        $this->serverAddress = $serverAddress;
    }

    /**
     * @param QueryInterface $query
     *
     * @return RequestInterface
     */
    protected function normalizeRequest(
        QueryInterface $query
    ): RequestInterface {
        $uri = new Uri($this->serverAddress);
        $uri = $uri->withPath($query->uri());
        $normalize = UriNormalizer::normalize(
            $uri,
            UriNormalizer::REMOVE_DUPLICATE_SLASHES
        );
        return new Request($query->httpMethod(), $normalize);
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
        $request = $this->normalizeRequest($query);
        $this->properties = $streamsProperties;
        try {
            $response = $this->sendRequest($query, $timeout, $debug, $request);
            if ($response->getStatusCode() == StatusCodeInterface::STATUS_OK) {
                return $query->queryResult($response);
            }
        } catch (GuzzleException $e) {
            throw new KsqlRestClientException($e->getMessage(), $e->getCode());
        }

        return $this->createErrorResult($request->getUri(), $response->getStatusCode());
    }

    /**
     * @param UriInterface $uri
     * @param int $statusCode
     *
     * @return ResultInterface
     */
    private function createErrorResult(
        UriInterface $uri,
        int $statusCode
    ): ResultInterface {
        $errorMessage['error_code'] = $statusCode;
        $errorMessage['message'] = "The server returned an unexpected error.";
        if ($statusCode === StatusCodeInterface::STATUS_NOT_FOUND) {
            $errorMessage['message'] = "Path not found. Path='" . $uri->getPath() . "'. "
                . "Check your ksql http url to make sure you are connecting to a ksql server.";
        }
        if ($statusCode === StatusCodeInterface::STATUS_UNAUTHORIZED) {
            $errorMessage['message'] = "Could not authenticate successfully with the supplied credentials.";
        }
        if ($statusCode === StatusCodeInterface::STATUS_FORBIDDEN) {
            $errorMessage['message'] = "You are forbidden from using this cluster.";
        }

        return new KsqlErrorMapper($errorMessage);
    }

    /**
     * @param QueryInterface $query
     * @param int $timeout
     * @param bool $debug
     *
     * @return array
     */
    protected function getOptions(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): array {
        $requestBody = $query->toArray();
        if ($query->hasProperties()) {
            $properties = ($this->properties instanceof LocalProperties) ? $this->properties->toArray() : new stdClass(
            );
            $requestBody = array_merge($query->toArray(), ['streamsProperties' => $properties]);
        }
        return [
            RequestOptions::TIMEOUT => $timeout,
            RequestOptions::BODY => json_encode($requestBody),
            RequestOptions::DEBUG => $debug,
        ];
    }

    /**
     * @param array $options
     */
    public function setOptions(
        array $options
    ): void {
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
            RequestOptions::HEADERS => [
                'User-Agent' => $this->userAgent(),
                'Accept' => \Ytake\KsqlClient\ClientInterface::REQUEST_ACCEPT,
                'Content-Type' => 'application/json; charset=utf-8',
            ],
        ];
    }

    /**
     * @param QueryInterface $query
     * @param int $timeout
     * @param bool $debug
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function sendRequest(
        QueryInterface $query,
        int $timeout,
        bool $debug,
        RequestInterface $request
    ): ResponseInterface {
        $options = $this->getOptions($query, $timeout, $debug);
        if ($this->hasUserCredentials) {
            $credentials = $this->getAuthCredentials();
            $options = array_merge(
                $options,
                [
                    'auth' => [$credentials->getUserName(), $credentials->getPassword()],
                ]
            );
        }
        return $this->client->send(
            $request,
            array_merge($options, $this->options)
        );
    }
}
