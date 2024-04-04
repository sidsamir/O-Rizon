<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Trip;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PostType extends AbstractType
{
    // Construit le formulaire avec les champs nécessaires.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du post',
                'empty_data' => '',
            ])        // Champ pour le titre de l'article

            ->add('summary', TextType::class, [
                'label' => 'Résumé',
                'empty_data' => '',
            ])      // Champ pour le résumé de l'article

            ->add('budget', IntegerType::class, [
                'label' => 'Prix',
                'empty_data' => '0',
            ])       // Champ pour le budget de l'article

            ->add('imageFile', VichFileType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'download_label' => 'Télécharger',
                'empty_data' => '',
            ])  // Champ pour l'image de l'article

                // Champ pour le lien de l'article

            ->add('city', TextType::class, [
                'label' => 'Ville',
                'empty_data' => '',
            ])         // Champ pour la ville de l'article

            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'empty_data' => '',
            ])        // Champ pour l'adresse de l'article

            ->add('date', BirthdayType::class, [
                'label' => 'Date de la sortie',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'empty_data' => (new \DateTimeImmutable())->format('d/m/Y'),
                'invalid_message' => 'Veuillez entrer une date de sortie valide',
            ]);         // Champ pour la date de l'article

            // ->add('created_at', BirthdayType::class, [
            //     'label'         => 'Date de création',
            //     'widget'        => 'single_text',
            //     'input'         => 'datetime_immutable',
            //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
            //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
            // ])   // Champ pour la date de création de l'article
            // ->add('updated_at', BirthdayType::class, [
            //     'label'         => 'Date de mise à jour',
            //     'widget'        => 'single_text',
            //     'input'         => 'datetime_immutable',
            //     'empty_data'    => (new \DateTimeImmutable())->format('d/m/Y'),
            //     'invalid_message'   => 'Veuillez entrer une date de sortie valide',
            // ])   // Champ pour la date de mise à jour de l'article

            // Champ pour lier l'article à une entité Trip
            // ->add('trip', EntityType::class, [
            //     'class' => Trip::class,       // Classe associée (Trip)
            //     'choice_label' => 'title',     // Champ utilisé comme libellé dans le formulaire ici le titre
            //     'label'         => 'Voyage',
            // ])

            // Champ pour lier l'article à une entité User (participant)
            // ->add('participant', EntityType::class, [
            //     'class' => User::class,        // Classe associée (User)
            //     'choice_label' => 'id',        // Champ utilisé comme libellé dans le formulaire
            //     'label' => 'Nombre de participant',
            // ]);
    }

    // Configure les options du formulaire.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,    // Classe associée aux données du formulaire (Post)
        ]);
    }
}
