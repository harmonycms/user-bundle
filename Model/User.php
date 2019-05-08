<?php

namespace Harmony\Bundle\UserBundle\Model;

use DateTime;
use Harmony\Bundle\UserBundle\Security\UserInterface;
use Serializable;
use function array_merge;
use function array_search;
use function array_unique;
use function array_values;
use function in_array;
use function method_exists;
use function serialize;
use function time;
use function unserialize;

/**
 * Class User
 *
 * @package Harmony\Bundle\UserBundle\Model
 */
abstract class User implements UserInterface, Serializable
{

    /**
     * @var string|int $id
     */
    protected $id;

    /**
     * @var DateTime $passwordRequestedAt
     */
    protected $passwordRequestedAt;

    /**
     * @var string $resetToken
     */
    protected $resetToken;

    /**
     * @var DateTime $expiredAt
     */
    protected $expiredAt;

    /**
     * @var DateTime $deletedAt
     */
    protected $deletedAt;

    /**
     * @var boolean $isLocked
     */
    protected $isLocked = false;

    /**
     * @var boolean $isPasswordResetRequired
     */
    protected $isPasswordResetRequired = false;

    /**
     * @var string $plainPassword
     */
    protected $plainPassword;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var array $roles
     */
    private $roles = [];

    /**
     * Get the value of username.
     *
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username.
     *
     * @param mixed $username
     *
     * @return UserInterface
     */
    public function setUsername($username): UserInterface
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password.
     *
     * @param mixed $password
     *
     * @return UserInterface
     */
    public function setPassword($password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get all roles, also from groups.
     * This method is internally used by Symfony Security itself to retrieve all roles of current user.
     *
     * @return array
     * @see getUserRoles() method to the user's list of roles only.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        if (method_exists($this, 'getGroups')) {
            /** @var Group $group */
            foreach ($this->getGroups() as $group) {
                $roles = array_merge($roles, $group->getRoles());
            }
        }

        // guarantee every user at least has ROLE_USER
        $roles[] = UserInterface::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * Get the value of roles only.
     *
     * @return array
     */
    public function getUserRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = UserInterface::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * Set the value of roles.
     *
     * @param array $roles
     *
     * @return UserInterface
     */
    public function setRoles(array $roles): UserInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g. $securityContext->isGranted('ROLE_USER');.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    /**
     * @param string $role
     *
     * @return UserInterface
     */
    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return UserInterface
     */
    public function addRole($role): UserInterface
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Get the value of password_requested_at.
     *
     * @return mixed
     */
    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Set the value of password_requested_at.
     *
     * @param mixed $passwordRequestedAt
     *
     * @return UserInterface
     */
    public function setPasswordRequestedAt(?DateTime $passwordRequestedAt): UserInterface
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get the value of reset_token.
     *
     * @return mixed
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * Set the value of reset_token.
     *
     * @param mixed $token
     *
     * @return UserInterface
     */
    public function setResetToken(?string $token): UserInterface
    {
        $this->resetToken = $token;

        return $this;
    }

    /**
     * Get the value of expired_at.
     *
     * @return mixed
     */
    public function getExpiredAt(): ?DateTime
    {
        return $this->expiredAt;
    }

    /**
     * Set the value of expired_at.
     *
     * @param mixed $expiredAt
     *
     * @return UserInterface
     */
    public function setExpiredAt(?DateTime $expiredAt): UserInterface
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get the value of deleted_at.
     *
     * @return mixed
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set the value of deleted_at.
     *
     * @param mixed $deletedAt
     *
     * @return UserInterface
     */
    public function setDeletedAt(?DateTime $deletedAt): UserInterface
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get the value of is_locked.
     *
     * @return mixed
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * Set the value of is_locked.
     *
     * @param mixed $isLocked
     *
     * @return UserInterface
     */
    public function setIsLocked($isLocked): UserInterface
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get the value of is_password_reset_required.
     *
     * @return mixed
     */
    public function isPasswordResetRequired(): bool
    {
        return $this->isPasswordResetRequired;
    }

    /**
     * Set the value of is_password_reset_required.
     *
     * @param mixed $isPasswordResetRequired
     *
     * @return UserInterface
     */
    public function setIsPasswordResetRequired($isPasswordResetRequired): UserInterface
    {
        $this->isPasswordResetRequired = $isPasswordResetRequired;

        return $this;
    }

    /**
     * Sets plain-text password.
     *
     * @param null|string $plainPassword
     *
     * @return UserInterface
     */
    public function setPlainPassword(?string $plainPassword): UserInterface
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Returns plain-text password.
     *
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt(): string
    {
        return '';
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return UserInterface
     */
    public function eraseCredentials(): UserInterface
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * Checks if password request is expired.
     *
     * @param int $ttl
     *
     * @return bool
     */
    public function isPasswordRequestExpired(int $ttl): bool
    {
        return null === $this->getPasswordRequestedAt() ||
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl <= time();
    }

    /**
     * Checks if a user account is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->getDeletedAt() && $this->getDeletedAt()->getTimestamp() <= time();
    }

    /**
     * Checks if a user account is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return null !== $this->getExpiredAt() && $this->getExpiredAt()->getTimestamp() <= time();
    }

    /**
     * String representation of object
     *
     * @link  https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->expiredAt,
            $this->deletedAt,
            $this->isLocked,
        ]);
    }

    /**
     * Constructs the object
     *
     * @link  https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>The string representation of the object.</p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->username,
            $this->password,
            $this->expiredAt,
            $this->deletedAt,
            $this->isLocked,
        ]
            = unserialize($serialized, ['allowed_classes' => false]);
    }
}
