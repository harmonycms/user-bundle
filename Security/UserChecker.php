<?php

namespace Harmony\Bundle\UserBundle\Security;

use Doctrine\Common\Persistence\ManagerRegistry;
use Harmony\Bundle\UserBundle\Exception\AccountDeletedException;
use Harmony\Bundle\UserBundle\Exception\AccountLockedException;
use Harmony\Bundle\UserBundle\Exception\PasswordResetRequiredException;
use Harmony\Bundle\UserBundle\Model\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @see https://symfony.com/doc/current/security/user_checkers.html
 */
class UserChecker implements UserCheckerInterface
{

    private $registry;

    /**
     * UserChecker constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface|User $user
     *
     * @throws \Exception
     */
    public function checkPreAuth(UserInterface $user)
    {
        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted()) {
            throw new AccountDeletedException();
        }

        // user account is locked, the user may be notified
        if ($user->isLocked()) {
            throw new AccountLockedException();
        }

        // user account is expired, the user may be notified
        if ($user->isExpired()) {
            throw new AccountExpiredException();
        }

        // password reset is required
        if ($user->isPasswordResetRequired()) {
            // generate then store a reset token in the user entity
            $token = TokenGenerator::generateToken();
            $user->setResetToken($token);
            $this->registry->getManager()->persist($user);
            $this->registry->getManager()->flush();

            // store the reset token in the exception
            $exception = new PasswordResetRequiredException();
            $exception->setResetToken($token);

            throw $exception;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {
    }
}
