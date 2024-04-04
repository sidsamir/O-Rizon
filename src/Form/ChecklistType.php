<?php

namespace App\Form;

use App\Entity\Checklist;
use App\Entity\Trip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChecklistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', TextType::class, [
                'label' => 'Tâche',
                'empty_data' => '',
                'attr' => [
                    'class' => 'block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150 ease-in-out',
                    'placeholder' => 'Entrez une nouvelle tâche ici...',
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-semibold text-gray-700 mb-2',
                ],
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Validé' => 1,
                    'Non validé' => 0,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'mt-2',
                ],
                // Pour chaque choix, on personnalise via le block_prefix dans le template Twig si nécessaire.
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition ease-in-out duration-150',
                ],
            ]);

        // ->add('created_at', BirthdayType::class, [
        //     'label'         => 'Date de création',
        //     'widget'        => 'single_text',
        //     'input'         => 'datetime_immutable',
        //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
        //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
        // ])
        // ->add('updated_at', BirthdayType::class, [
        //     'label'         => 'Date de mise à jour',
        //     'widget'        => 'single_text',
        //     'input'         => 'datetime_immutable',
        //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
        //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
        // ])
        // ->add('trip', EntityType::class, [
        //     'class' => Trip::class,
        //     'choice_label' => 'title',
        // ]);
        // ->add('participant', EntityType::class, [
        //     'class' => User::class,
        //     'choice_label' => 'id',
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Checklist::class,
        ]);
    }
}
