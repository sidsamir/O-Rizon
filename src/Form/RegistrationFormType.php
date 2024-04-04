<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champ pour l'adresse e-mail de l'utilisateur
        $builder
            ->add('email', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse e-mail.']),
                    new Assert\Email(['message' => 'Le format de l\'adresse e-mail n\'est pas valide.']),
                ],
            ])
            ->add('firstName', null, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom.']),
                    new Length([
                        'min' => 2,
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom.']),
                    new Length([
                        'min' => 2,
                    ]),
                ],
            ])

            ->add('userName', null, [
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un nom d\'utilisateur.']),
                    new Length([
                        'min' => 2,
                    ]),
                ],
            ])

            ->add('Password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field w-full ml-2 placeholder-gray-400 border-2 border-gray-300 focus:border-green-500 focus:outline-none rounded-md p-2']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe',
                    'toggle' => true,
                ],
                'second_options' => ['label' => 'Confirmer le mot de passe',
                    'toggle' => true, ],
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 36,
                    ]),
                    new Regex([
                        'pattern' => "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/",
                        'message' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre.',
                    ]),
                ],
            ]);

        // TODO à remettre en place quand les conditions générales seront en place
        /*$builder->add('agreeTerms', CheckboxType::class, [
            'mapped' => false, // Ne pas mapper directement à l'entité User
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter nos conditions.',
                ]),
            ],
        ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
