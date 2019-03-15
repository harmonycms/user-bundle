<?php

namespace Harmony\Bundle\UserBundle\Model;

use Harmony\Bundle\UserBundle\Security\UserInterface;

/**
 * Class User
 *
 * @package Harmony\Bundle\UserBundle\Model
 */
abstract class User implements UserInterface, \Serializable
{

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
    private $roles = [self::ROLE_USER];

    /**
     * @var \DateTime $passwordRequestedAt
     */
    protected $passwordRequestedAt;

    /**
     * @var string $resetToken
     */
    protected $resetToken;

    /**
     * @var \DateTime $expiredAt
     */
    protected $expiredAt;

    /**
     * @var \DateTime $deletedAt
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
     * @return User
     */
    public function setUsername($username)
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
     * @return User
     */
    public function setPassword($password)
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
     * @return User
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * Set the value of roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of password_requested_at.
     *
     * @return mixed
     */
    public function getPasswordRequestedAt(): ?\DateTime
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
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): UserInterface
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
    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    /**
     * Set the value of expired_at.
     *
     * @param mixed $expiredAt
     *
     * @return User
     */
    public function setExpiredAt(?\DateTime $expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * Get the value of deleted_at.
     *
     * @return mixed
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set the value of deleted_at.
     *
     * @param mixed $deletedAt
     *
     * @return User
     */
    public function setDeletedAt(?\DateTime $deletedAt)
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
     * @return User
     */
    public function setIsLocked($isLocked)
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
     * @return User
     */
    public function setIsPasswordResetRequired($isPasswordResetRequired)
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
     */
    public function eraseCredentials()
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
