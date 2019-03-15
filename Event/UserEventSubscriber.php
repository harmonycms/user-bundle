<?php

namespace Harmony\UserBundle\Event;

use Harmony\UserBundle\Model\UserManager;
use Harmony\UserBundle\Security\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class UserEventSubscriber
 *
 * @package Harmony\UserBundle\Event
 */
class UserEventSubscriber implements EventSubscriber
{

    /** @var UserManager $manager */
    private $manager;

    /**
     * UserEventSubscriber constructor.
     *
     * @param UserManager $manager
     */
    public function __construct(UserManager $manager)
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