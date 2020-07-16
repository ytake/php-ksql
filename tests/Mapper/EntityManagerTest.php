<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Mapper\EntityManager;
use Ytake\KsqlClient\Entity\EntityInterface;

final class EntityManagerTest extends TestCase
{
    private $json = [
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
            $decode = \GuzzleHttp\json_decode(file_get_contents($row), true);
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

    /**
     * @expectedException Ytake\KsqlClient\Exception\UnrecognizedPropertyException
     * @expectedExceptionMessage Unrecognized field akka.
     */
    public function testShouldShouldThrowUnrecognizedPropertyException(): void
    {
        $json = array_merge($this->json, [__DIR__ . '/../resources/unknown.json',]);
        foreach ($json as $row) {
            $decode = \GuzzleHttp\json_decode(file_get_contents($row), true);
            if (!array_key_exists('@type', $decode)) {
                foreach ($decode as $item) {
                    (new EntityManager($item))->map();
                }
            }
        }
    }

    /**
     * @expectedException Ytake\KsqlClient\Exception\UnknownJsonObjectsException
     * @expectedExceptionMessage Unknown json objects.
     */
    public function testShouldShouldThrowUnknownJsonObjectsException(): void
    {
        $json = array_merge($this->json, [__DIR__ . '/../resources/nothing.json',]);
        foreach ($json as $row) {
            $decode = \GuzzleHttp\json_decode(file_get_contents($row), true);
            if (!array_key_exists('@type', $decode)) {
                foreach ($decode as $item) {
                    (new EntityManager($item))->map();
                }
            }
        }
    }
}
