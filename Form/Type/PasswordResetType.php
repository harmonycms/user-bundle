<?php

namespace Harmony\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PasswordResetType
 *
 * @package Harmony\UserBundle\Form\Type
 */
class PasswordResetType extends AbstractType
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
        $builder->add('plainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'options'         => ['translation_domain' => 'UserBundle'],
            'first_options'   => [
                'label' => 'password.reset.new_password',
                'attr'  => ['placeholder' => 'password.reset.new_password']
            ],
            'second_options'  => [
                'label' => 'password.reset.new_password_confirmation',
                'attr'  => ['placeholder' => 'password.reset.new_password_confirmation']
            ],
            'invalid_message' => 'password.reset.password_mismatch',
        ]);
    }
}
