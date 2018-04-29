<?php
declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Trait MockClientTrait
 */
trait MockClientTrait
{
    /**
     * @param int         $statusCode
     * @param string|null $body
     * @param array       $options
     *
     * @return Client
     */
    public function mockClient(int $statusCode, string $body = null, array $options = []): Client
    {
        $mock = new MockHandler([
            new Response($statusCode, $options, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    /**
     * @return Client
     */
    public function throwRequestExceptionClient(): Client
    {
        $mock = new MockHandler([
            new RequestException("Error Communicating with Server", new Request('GET', 'test')),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    /**
     * @return Client
     */
    public function throwClientExceptionClient(): Client
    {
        $mock = new MockHandler([
            new \GuzzleHttp\Exception\ClientException("Error Communicating with Server", new Request('POST', 'test')),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
