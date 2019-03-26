<?php

namespace Harmony\Bundle\UserBundle\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Harmony\Bundle\UserBundle\Manager\UserManagerInterface;
use Harmony\Bundle\UserBundle\Security\UserInterface;

/**
 * Class UserEventSubscriber
 *
 * @package Harmony\Bundle\UserBundle\Event
 */
class UserEventSubscriber implements EventSubscriber
{

    /** @var UserManagerInterface $manager */
    private $manager;

    /**
     * UserEventSubscriber constructor.
     *
     * @param UserManagerInterface $manager
     */
    public function __construct(UserManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->manager->hashPassword($object);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->manager->hashPassword($object);
            $meta = $args->getObjectManager()->getClassMetadata(get_class($object));
            $args->getObjectManager()->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $object);
        }
    }
}