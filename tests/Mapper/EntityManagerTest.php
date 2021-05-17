<?php

declare(strict_types=1);

namespace Tests\Mapper;

use GuzzleHttp\Utils;
use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Entity\EntityInterface;
use Ytake\KsqlClient\Exception\UnknownJsonObjectsException;
use Ytake\KsqlClient\Exception\UnrecognizedPropertyException;
use Ytake\KsqlClient\Mapper\EntityManager;

use function array_key_exists;
use function array_merge;
use function file_get_contents;

final class EntityManagerTest extends TestCase
{
    /** @var string[] */
    private array $json = [
        __DIR__ . '/../resources/currentStatus.json',
        __DIR__ . '/../resources/desc.json',
        __DIR__ . '/../resources/kafka_topics.json',
        __DIR__ . '/../resources/properties.json',
        __DIR__ . '/../resources/queries.json',
        __DIR__ . '/../resources/query_desc.json',
        __DIR__ . '/../resources/streamslist.json',
        __DIR__ . '/../resources/tables.json',
    ];

    public function testShouldReturnMapperInstance(): void
    {
        foreach ($this->json as $row) {
            $decode = Utils::jsonDecode(file_get_contents($row), true);
            if (!array_key_exists('@type', $decode)) {
                foreach ($decode as $item) {
                    $this->assertInstanceOf(
                        EntityInterface::class,
                        (new EntityManager($item))->map()
                    );
                }
            }
        }
    }

    public function testShouldShouldThrowUnrecognizedPropertyException(): void
    {
        $this->expectException(UnrecognizedPropertyException::class);
        $this->expectExceptionMessage('Unrecognized field akka.');
        $json = array_merge($this->json, [__DIR__ . '/../resources/unknown.json',]);
        foreach ($json as $row) {
            $decode = Utils::jsonDecode(file_get_contents($row), true);
            if (!array_key_exists('@type', $decode)) {
                foreach ($decode as $item) {
                    (new EntityManager($item))->map();
                }
            }
        }
    }

    public function testShouldShouldThrowUnknownJsonObjectsException(): void
    {
        $this->expectException(UnknownJsonObjectsException::class);
        $this->expectExceptionMessage('Unknown json objects.');
        $json = array_merge($this->json, [__DIR__ . '/../resources/nothing.json',]);
        foreach ($json as $row) {
            $decode = Utils::jsonDecode(file_get_contents($row), true);
            if (!array_key_exists('@type', $decode)) {
                foreach ($decode as $item) {
                    (new EntityManager($item))->map();
                }
            }
        }
    }
}
