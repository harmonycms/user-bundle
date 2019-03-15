<?php

namespace Harmony\Bundle\UserBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class AccountLockedException
 *
 * @package Harmony\Bundle\UserBundle\Exception
 */
class AccountLockedException extends AccountStatusException
{

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'login.exception.locked_account';
    }
}
