<?php
declare(strict_types=1);

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
class RestClient
{
    const VERSION = '0.1.0';

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
            'headers' => [
                'User-Agent' => $this->userAgent(),
                'Accept'     => 'application/json',
            ],
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
}
