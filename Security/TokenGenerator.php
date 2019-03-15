<?php

namespace Harmony\Bundle\UserBundle\Security;

/**
 * Class TokenGenerator
 *
 * @package Harmony\Bundle\UserBundle\Security
 */
class TokenGenerator
{

    /**
     * @return string
     * @throws \Exception
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
