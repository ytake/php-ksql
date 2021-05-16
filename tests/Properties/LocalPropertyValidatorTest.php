<?php

declare(strict_types=1);

namespace Tests\Properties;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Config\ConsumerConfig;
use Ytake\KsqlClient\Properties\LocalPropertyValidator;

final class LocalPropertyValidatorTest extends TestCase
{
    /** @var LocalPropertyValidator */
    private LocalPropertyValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LocalPropertyValidator(["immutable-1", "immutable-2"]);
    }

    public function testShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate("immutable-2", "anything");
    }

    public function testShouldNotThrowOnMutableProp(): void
    {
        $this->assertNull($this->validator->validate("mutable-1", "anything"));
    }

    public function testShouldThrowOnNoneOffsetReset(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validate(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, "none");
    }

    public function testShouldNotThrowOnOtherOffsetReset(): void
    {
        $this->assertNull(
            $this->validator->validate(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, "caught-by-normal-mech")
        );
    }
}
