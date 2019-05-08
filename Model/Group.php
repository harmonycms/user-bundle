<?php

namespace Harmony\Bundle\UserBundle\Model;

use Harmony\Bundle\UserBundle\Group\GroupInterface;

/**
 * Class Group
 *
 * @package Harmony\Bundle\UserBundle\Model
 */
abstract class Group implements GroupInterface
{

    /**
     * @var string|int $id
     */
    protected $id;

    /**
     * @var null|string $name
     */
    protected $name;

    /**
     * @var array $roles
     */
    protected $roles = [];

    /**
     * @return string|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function setName(string $name): GroupInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add role
     *
     * @param string $role
     *
     * @return GroupInterface
     */
    public function addRole(string $role): GroupInterface
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    /**
     * Get Roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set Roles
     *
     * @param array $roles
     *
     * @return GroupInterface
     */
    public function setRoles(array $roles): GroupInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Remove role
     *
     * @param string $role
     *
     * @return GroupInterface
     */
    public function removeRole(string $role): GroupInterface
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Returns group name
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}