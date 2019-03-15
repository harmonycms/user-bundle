<?php

namespace Harmony\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PasswordRequestType
 *
 * @package Harmony\UserBundle\Form\Type
 */
class PasswordRequestType extends AbstractType
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
        $builder->add('username', TextType::class, [
            'translation_domain' => 'UserBundle',
            'label'              => 'password.request.username.label',
            'attr'               => [
                'placeholder'  => 'password.request.username.placeholder',
                'autocomplete' => 'off',
            ],
        ]);
    }
}
