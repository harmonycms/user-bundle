<?php

namespace Harmony\UserBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class AccountDeletedException
 *
 * @package Harmony\UserBundle\Exception
 */
class AccountDeletedException extends AccountStatusException
{

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'login.exception.deleted_account';
    }
}
