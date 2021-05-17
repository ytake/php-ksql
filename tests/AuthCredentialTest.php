<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ytake\KsqlClient\AuthCredential;

final class AuthCredentialTest extends TestCase
{
    public function testShouldReturnAuthCredential(): void
    {
        $credential = new AuthCredential('testing', 'testingPassword');
        $this->assertSame('testing', $credential->getUserName());
        $this->assertSame('testingPassword', $credential->getPassword());
    }
}
