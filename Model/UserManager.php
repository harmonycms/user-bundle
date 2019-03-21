<?php

namespace Harmony\Bundle\UserBundle\Model;

use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function base64_encode;
use function random_bytes;
use function rtrim;
use function str_replace;
use function strlen;

/**
 * Class UserManager
 *
 * @package Harmony\Bundle\UserBundle\Model
 */
class UserManager
{

    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var string $userClass */
    protected $userClass;

    /** @var PasswordEncoderInterface $encoderFactory */
    private $encoderFactory;

    /**
     * UserManager constructor.
     *
     * @param ManagerRegistry         $registry
     * @param EncoderFactoryInterface $encoderFactory
     * @param string                  $userClass
     */
    public function __construct(ManagerRegistry $registry, EncoderFactoryInterface $encoderFactory, string $userClass)
    {
        $this->registry       = $registry;
        $this->encoderFactory = $encoderFactory;
        $this->userClass      = $userClass;
    }

    /**
     * @return UserInterface
     * @throws Exception
     */
    public function createUser(): UserInterface
    {
        $class = $this->registry->getManager()->getMetadataFactory()->getMetadataFor($this->userClass)->getName();

        return new $class();
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