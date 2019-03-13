<?php

namespace Harmony\UserBundle\Security;

class TokenGenerator
{
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}
