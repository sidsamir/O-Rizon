<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\Trip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Le titre de votre critique',
            ])
            ->add('summary', TextType::class, [
                'label' => 'Laissez votre avis ici',
            ]);
        // ->add('created_at', BirthdayType::class, [
        //     'label'         => 'Date de la sortie',
        //     'widget'        => 'single_text',
        //     'input'         => 'datetime_immutable',
        //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
        //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
        // ])
        // ->add('updated_at', BirthdayType::class, [
        //     'label'         => 'Date de création',
        //     'widget'        => 'single_text',
        //     'input'         => 'datetime_immutable',
        //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
        //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
        // ]);
        // ->add('trip', EntityType::class, [
        //     'class' => Trip::class,
        //     'choice_label' => 'title',
        // ])
        // ->add('participant', EntityType::class, [
        //     'class' => User::class,
        //     'choice_label' => 'id',
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
