<?php

namespace Harmony\Bundle\UserBundle\Manager;

use Harmony\Bundle\UserBundle\Model\User;
use Harmony\Bundle\UserBundle\Security\UserInterface;

/**
 * Interface UserManagerInterface
 *
 * @package Harmony\Bundle\UserBundle\Manager
 */
interface UserManagerInterface
{

    /**
     * Get new instance of User.
     *
     * @return UserInterface|User
     */
    public function getInstance(): UserInterface;

    /**
     * Create new user.
     *
     * @param UserInterface|User $user
     */
    public function create(UserInterface $user): void;

    /**
     * Update existing user.
     *
     * @param UserInterface|User $user
     */
    public function update(UserInterface $user): void;

    /**
     * @param string $email
     *
     * @return UserInterface
     */
    public function getUser(string $email): UserInterface;
}