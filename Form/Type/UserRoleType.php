<?php

namespace Harmony\Bundle\UserBundle\Form\Type;

use Harmony\Bundle\UserBundle\Security\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserRoleType
 *
 * @package Harmony\Bundle\UserBundle\Form\Type
 */
class UserRoleType extends AbstractType
{

    /**
     * @var array
     */
    private $roleHierarchy;

    /**
     * UserRoleType constructor.
     *
     * @param array $roleHierarchy
     */
    public function __construct(array $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $roles = [UserInterface::ROLE_USER => UserInterface::ROLE_USER];
        foreach ($this->roleHierarchy as $key => $value) {
            $roles[$key] = $key;
        }
        ksort($roles);

        $resolver->setDefaults(['expanded' => false, 'multiple' => true, 'choices' => $roles]);
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

}