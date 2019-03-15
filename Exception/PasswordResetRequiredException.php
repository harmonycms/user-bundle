<?php

namespace Harmony\Bundle\UserBundle\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class PasswordResetRequiredException
 *
 * @package Harmony\Bundle\UserBundle\Exception
 */
class PasswordResetRequiredException extends AccountStatusException
{

    /**
     * @var string
     */
    private $resetToken;

    /**
     * Get the value of resetToken.
     *
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * Set the value of resetToken.
     *
     * @param string $resetToken
     */
    public function setResetToken(string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'login.exception.password_reset_required';
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
            $this->resetToken,
            parent::serialize(),
        ]);
    }

    /**
     * Constructs the object
     *
     * @link  https://php.net/manual/en/serializable.unserialize.php
     *
     * @param $str
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($str)
    {
        list($this->resetToken, $parentData) = unserialize($str);

        parent::unserialize($parentData);
    }
}
