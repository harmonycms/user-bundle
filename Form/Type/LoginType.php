<?php

namespace Harmony\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginType
 *
 * @package Harmony\Bundle\UserBundle\Form\Type
 */
class LoginType extends AbstractType
{

    /**
     * Builds the form.
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', TextType::class, [
            'translation_domain' => 'UserBundle',
            'label'              => 'login.username.label',
            'attr'               => [
                'placeholder'  => 'login.username.placeholder',
                'autocomplete' => 'off',
            ],
        ])->add('_password', PasswordType::class, [
            'translation_domain' => 'UserBundle',
            'label'              => 'login.password.label',
            'attr'               => [
                'placeholder'  => 'login.password.placeholder',
                'autocomplete' => 'off',
            ],
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
