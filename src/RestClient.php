<?php
declare(strict_types=1);

namespace Ytake\KsqlClient;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriNormalizer;
use Ytake\KsqlClient\Exception\KsqlRestClientException;
use Ytake\KsqlClient\Query\QueryInterface;
use Ytake\KsqlClient\Result\AbstractResult;
use Ytake\KsqlClient\Result\ErrorResult;

/**
 * Class RestClient
 */
class RestClient
{
    const USER_AGENT = 'PHP-KSQLClient';

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
                'User-Agent' => self::USER_AGENT,
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
     * @return AbstractResult|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestQuery(
        QueryInterface $query,
        int $timeout = 500000,
        bool $debug = false
    ): AbstractResult {
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

            return new ErrorResult($response);
        } catch (ClientException $e) {
            throw new KsqlRestClientException($e->getMessage(), $e->getCode(), $e);
        }
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
}
