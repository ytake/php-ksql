<?php
declare(strict_types=1);

namespace Ytake\KsqlClient;

/**
 * for KSQL basic
 * Class AuthCredential
 */
final class AuthCredential
{
    /** @var string */
    private $userName;

    /** @var string */
    private $password;

    /**
     * @param string $userName
     * @param string $password
     */
    public function __construct(string $userName, string $password)
    {
        $this->userName = $userName;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
