<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\Properties\LocalProperties;
use Ytake\KsqlClient\Properties\LocalPropertyValidator;

final class LocalPropertiesTest extends TestCase
{
    /** @var LocalProperties */
    private $properties;

    protected function setUp()
    {
        $this->properties = new LocalProperties(
            [
                "prop-1" => "initial-val-1",
                "prop-2" => "initial-val-2"
            ],
            new LocalPropertyValidator()
        );
    }

    public function testShouldReturnInitValues(): void
    {
        $this->assertSame('initial-val-1', $this->properties->get('prop-1'));
        $this->assertSame('initial-val-2', $this->properties->get('prop-2'));
    }

    public function testShouldUnsetInitValue(): void
    {
        $this->properties->remove('prop-1');
        $this->assertNull($this->properties->get('prop-1'));
        $this->assertSame('initial-val-2', $this->properties->get('prop-2'));
    }

    public function testShouldReturnOverrideInitValues(): void
    {
        $this->properties->set('prop-1', 'new-val');
        $this->assertSame('new-val', $this->properties->get('prop-1'));
        $this->assertSame('initial-val-2', $this->properties->get('prop-2'));
    }

    public function testShouldUnsetOverrideValue(): void
    {
        $this->properties->set('prop-1', 'new-val');
        $this->properties->remove('prop-1');
        $this->assertNull($this->properties->get('prop-1'));
        $this->assertSame('initial-val-2', $this->properties->get('prop-2'));
    }

    public function testShouldReturnPropertiesArray(): void
    {
        $this->assertSame(
            [
                "prop-1" => "initial-val-1",
                "prop-2" => "initial-val-2"
            ],
            $this->properties->toArray()
        );
    }
}
