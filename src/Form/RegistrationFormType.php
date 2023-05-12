<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;



class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a username.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Your username should be at least {{ limit }} characters.',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email address.',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please confirm your password.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters.',
                        'max' => 4096,
                    ]),
                    new Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            $password = $context->getRoot()->get('password')->getData();
                            if ($password !== $value) {
                                $context->buildViolation('The password fields do not match.')
                                    ->atPath('confirmPassword')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Set your data class for the form
            'data_class' => User::class,
        ]);
    }
}
