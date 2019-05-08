<?php

namespace Harmony\Bundle\UserBundle\Group;

use Doctrine\Common\Collections\ArrayCollection;
use Traversable;

/**
 * Trait GroupableUserTrait
 *
 * @package Harmony\Bundle\UserBundle\Group
 */
trait GroupableUserTrait
{

    /**
     * @var GroupInterface[]|ArrayCollection
     */
    protected $groups;

    /**
     * Gets the groups granted to the user.
     *
     * @return Traversable|ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup(string $name): bool
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * Add a group to the user groups.
     *
     * @param GroupInterface $group
     *
     * @return GroupableUserTrait
     */
    public function addGroup(GroupInterface $group): GroupableUserTrait
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * Remove a group from the user groups.
     *
     * @param GroupInterface $group
     *
     * @return GroupableUserTrait
     */
    public function removeGroup(GroupInterface $group): GroupableUserTrait
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }
}