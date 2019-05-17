<?php

namespace Harmony\Bundle\UserBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Harmony\Bundle\UserBundle\Security\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use function strlen;

/**
 * Class UserManager
 *
 * @package Harmony\Bundle\UserBundle\Manager
 */
class UserManager implements UserManagerInterface
{

    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var ObjectManager $manager */
    protected $manager;

    /** @var string $userClass */
    protected $userClass;

    /** @var PasswordEncoderInterface $encoderFactory */
    private $encoderFactory;

    /**
     * UserManager constructor.
     *
     * @param ManagerRegistry         $registry
     * @param ObjectManager           $manager
     * @param EncoderFactoryInterface $encoderFactory
     * @param string                  $userClass
     */
    public function __construct(ManagerRegistry $registry, ObjectManager $manager,
                                EncoderFactoryInterface $encoderFactory, string $userClass)
    {
        $this->registry       = $registry;
        $this->manager        = $manager;
        $this->encoderFactory = $encoderFactory;
        $this->userClass      = $userClass;
    }

    /**
     * @return UserInterface
     * @throws Exception
     */
    public function getInstance(): UserInterface
    {
        $class = $this->manager->getClassMetadata($this->userClass)->getName();

        return new $class();
    }

    /**
     * Create new user.
     *
     * @param UserInterface $user
     */
    public function create(UserInterface $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * Update existing user.
     *
     * @param UserInterface $user
     */
    public function update(UserInterface $user): void
    {
        $this->manager->flush();
    }

    /**
     * @param string $email
     *
     * @return null|UserInterface
     */
    public function getUser(string $email): ?UserInterface
    {
        return $this->registry->getRepository($this->userClass)->findOneBy(['email' => $email]);
    }

    /**
     * @param UserInterface $user
     *
     * @throws Exception
     */
    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();
        if (0 !== strlen($plainPassword)) {
            $hashedPassword = $this->encoderFactory->getEncoder($user)
                ->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($hashedPassword);
            $user->eraseCredentials();
        }
    }
}