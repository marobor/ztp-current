<?php

declare(strict_types=1);

/**
 * User type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserType.
 */
class UserType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'data' => $options['current_email'],
                    'label' => 'user_email',
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                        new Length(['min' => 3, 'max' => 200]),
                    ],
                ]
            )
            ->add(
                'current_password',
                PasswordType::class,
                [
                    'label' => 'user_current_password',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'password_fields_must_match',
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'first_options' => [
                        'label' => 'user_new_password',
                    ],
                    'second_options' => [
                        'label' => 'user_new_password_repeat',
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'submit_button',
                ]
            )
        ;
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('current_email', '');

        $resolver->setAllowedTypes('current_email', 'string');
    }
}
