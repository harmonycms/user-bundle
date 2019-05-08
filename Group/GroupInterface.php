<?php

namespace Harmony\Bundle\UserBundle\Group;

/**
 * Interface GroupInterface
 *
 * @package Harmony\Bundle\UserBundle\Group
 */
interface GroupInterface
{

    /**
     * @param string $role
     *
     * @return GroupInterface
     */
    public function addRole(string $role): GroupInterface;

    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param string $role
     *
     * @return GroupInterface
     */
    public function removeRole(string $role): GroupInterface;

    /**
     * @param string $name
     *
     * @return GroupInterface
     */
    public function setName(string $name): GroupInterface;

    /**
     * @param array $roles
     *
     * @return GroupInterface
     */
    public function setRoles(array $roles): GroupInterface;
}