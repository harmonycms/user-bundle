<?php

namespace Harmony\Bundle\UserBundle\Security;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function class_implements;
use function in_array;

/**
 * Class UserProvider
 *
 * @see     https://symfony.com/doc/current/security/user_provider.html#creating-a-custom-user-provider
 * @package Harmony\Bundle\UserBundle\Security
 */
class UserProvider implements UserProviderInterface
{

    /** @var ManagerRegistry $registry */
    private $registry;

    /** @var EncoderFactoryInterface $encoder */
    private $encoder;

    /** @var string $userClass */
    private $userClass;

    /**
     * UserProvider constructor.
     *
     * @param ManagerRegistry         $registry
     * @param EncoderFactoryInterface $encoder
     * @param                         $userClass
     */
    public function __construct(ManagerRegistry $registry, EncoderFactoryInterface $encoder, $userClass)
    {
        $this->registry  = $registry;
        $this->encoder   = $encoder;
        $this->userClass = $registry->getManager()->getClassMetadata($userClass)->getName();
    }

    /**
     * Loads the user for the given username.
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return $this->findUserByUsername($username);
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function findUserByUsername(string $username): UserInterface
    {
        $criteria = new Criteria();
        $criteria->orWhere($criteria->expr()->contains('username', $username))->orWhere($criteria->expr()
            ->contains('email', $username));

        $user = $this->registry->getRepository($this->userClass)->matching($criteria)->first();

        if (false === $user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * @param string $token
     *
     * @return UserInterface
     */
    public function findUserByResetToken(string $token): UserInterface
    {
        $user = $this->registry->getRepository($this->userClass)->findOneBy(['reset_token' => $token]);

        if (null === $user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return PasswordEncoderInterface
     */
    public function getEncoder(UserInterface $user): PasswordEncoderInterface
    {
        return $this->encoder->getEncoder($user);
    }

    /**
     * Refreshes the user.
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return in_array(UserInterface::class, class_implements($class), true);
    }
}
