<?php

namespace Harmony\Bundle\UserBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * Interface UserInterface
 *
 * @package Harmony\Bundle\UserBundle\Security
 */
interface UserInterface extends BaseUserInterface
{

    /** Roles constants */
    const ROLE_USER        = 'ROLE_USER';
    const ROLE_ADMIN       = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Get the value of id.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Get the value of email.
     *
     * @return null|string
     */
    public function getEmail(): ?string;

    /**
     * Get the value of password_requested_at.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime;

    /**
     * Set the value of password_requested_at.
     *
     * @param null|\DateTime $passwordRequestedAt
     *
     * @return self
     */
    public function setPasswordRequestedAt(?\DateTime $passwordRequestedAt): self;

    /**
     * Returns plain-text password.
     *
     * @return null|string
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets plain-text password.
     *
     * @param null|string $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self;

    /**
     * Get the value of reset_token.
     *
     * @return null|string
     */
    public function getResetToken(): ?string;

    /**
     * Set the value of reset_token.
     *
     * @param string|null $token
     *
     * @return self
     */
    public function setResetToken(?string $token): self;

    /**
     * Return true if user account is locked.
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * Return true if user account is deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool;

    /**
     * Return true if user account is expired.
     *
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Return true if user account has to reset its password at next authentication.
     *
     * @return bool
     */
    public function isPasswordResetRequired(): bool;

    /**
     * Return true if password request is expired.
     *
     * @param int $ttl
     *
     * @return bool
     */
    public function isPasswordRequestExpired(int $ttl): bool;

    /**
     * @param string
     *
     * @return bool
     */
    public function hasRole($role): bool;
}
