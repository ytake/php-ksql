<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Properties\LocalPropertyValidator;
use Ytake\KsqlClient\Config\ConsumerConfig;

/**
 * Class LocalPropertyValidatorTest
 */
final class LocalPropertyValidatorTest extends TestCase
{
    /** @var LocalPropertyValidator */
    private $validator;

    protected function setUp()
    {
        $this->validator = new LocalPropertyValidator(["immutable-1", "immutable-2"]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowException(): void
    {
        $this->validator->validate("immutable-2", "anything");
    }

    public function testShouldNotThrowOnMutableProp(): void
    {
        $this->assertNull($this->validator->validate("mutable-1", "anything"));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldThrowOnNoneOffsetReset(): void
    {
        $this->validator->validate(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, "none");
    }

    public function testShouldNotThrowOnOtherOffsetReset(): void
    {
        $this->assertNull(
            $this->validator->validate(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, "caught-by-normal-mech")
        );
    }
}
