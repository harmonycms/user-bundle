<?php

namespace Harmony\Bundle\UserBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType
 *
 * @package Harmony\Bundle\UserBundle\Form\Type
 */
class RegistrationFormType extends AbstractType
{

    /** @var string $userClass */
    protected $userClass;

    /**
     * RegistrationFormType constructor.
     *
     * @param ManagerRegistry $registry
     * @param string          $userClass
     */
    public function __construct(ManagerRegistry $registry, string $userClass)
    {
        $this->userClass = $registry->getManager()->getClassMetadata($userClass)->getName();
    }

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
        $builder->add('email', EmailType::class, [
            'label'              => 'registration.email.label',
            'translation_domain' => 'UserBundle',
            'attr'               => ['placeholder' => 'registration.email.placeholder']
        ])->add('username', TextType::class, [
            'label'              => 'registration.username.label',
            'translation_domain' => 'UserBundle',
            'attr'               => ['placeholder' => 'registration.username.placeholder']
        ])->add('plainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'mapped'          => false,
            'options'         => [
                'translation_domain' => 'UserBundle',
                'attr'               => ['autocomplete' => 'new-password'],
                'constraints'        => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max'        => 4096
                    ])
                ]
            ],
            'first_options'   => [
                'label' => 'registration.plain_password.label',
                'attr'  => ['placeholder' => 'registration.plain_password.placeholder']
            ],
            'second_options'  => [
                'label' => 'registration.password_confirmation.label',
                'attr'  => ['placeholder' => 'registration.password_confirmation.placeholder']
            ],
            'invalid_message' => 'fos_user.password.mismatch'
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => $this->userClass]);
    }
}