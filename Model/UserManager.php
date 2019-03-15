<?php

namespace Harmony\Bundle\UserBundle\Model;

use Exception;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserManager
 *
 * @package Harmony\Bundle\UserBundle\Model
 */
class UserManager
{

    /** @var string $userClass */
    protected $userClass;

    /** @var PasswordEncoderInterface $encoderFactory */
    private $encoderFactory;

    /**
     * UserManager constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param string                  $userClass
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, string $userClass)
    {
        $this->encoderFactory = $encoderFactory;
        $this->userClass      = $userClass;
    }

    /**
     * @return UserInterface
     * @throws Exception
     */
    public function createUser(): UserInterface
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function getClass()
    {
        if (!class_exists($this->userClass)) {
            throw new Exception('Class not found : ' . $this->userClass);
        }

        return new $this->userClass;
    }

    /**
     * @param UserInterface|User $user
     *
     * @throws \Exception
     */
    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();
        if (0 !== strlen($plainPassword)) {
            $salt = null;
            if (!($this->encoderFactory->getEncoder($user) instanceof BCryptPasswordEncoder)) {
                // salt is not used by bcrypt encoder
                $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            }
            $user->setSalt($salt);

            $hashedPassword = $this->encoderFactory->getEncoder($user)
                ->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
        }
    }
}