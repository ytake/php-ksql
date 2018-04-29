<?php
declare(strict_types=1);

use Ytake\KsqlClient\AuthCredential;

class AuthCredentialTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldReturnAuthCredential(): void
    {
        $credential = new AuthCredential('testing', 'testingPassword');
        $this->assertSame('testing', $credential->getUserName());
        $this->assertSame('testingPassword', $credential->getPassword());
    }
}
