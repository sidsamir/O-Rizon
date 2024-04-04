<?php

namespace App\Form;

use App\Entity\Friendship;
use App\Entity\Tag;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TripType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentUser = $this->security->getUser();
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du voyage',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le titre du voyage'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Décrivez votre voyage', 'rows' => 5],
            ])
            ->add('budget', IntegerType::class, [
                'label' => 'Budget (en €)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Budget prévu pour le voyage'],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut du voyage',
                'choices' => Trip::STATUS_CHOICES,
                'placeholder' => 'Sélectionnez le statut du voyage',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'placeholder' => 'Choisissez un pays',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('imageFile', VichFileType::class, [
                'label' => 'Image du voyage',
                'attr' => ['class' => 'form-control-file'],
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'download_label' => 'Télécharger l\'image actuelle',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ville principale du voyage'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse de rendez-vous'],
            ])
            ->add('date', BirthdayType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($currentUser) {
                    // Jointure pour récupérer les amis où l'utilisateur actuel est le demandeur
                    $qb = $er->createQueryBuilder('u');
                    $qb->leftJoin('u.receiverFriendships', 'rf')
                        ->leftJoin('u.friendships', 'f')
                        ->where($qb->expr()->orX(
                            $qb->expr()->andX('rf.requester = :user', 'rf.status = :status'),
                            $qb->expr()->andX('f.receiver = :user', 'f.status = :status')
                        ))
                        ->setParameter('user', $currentUser)
                        ->setParameter('status', Friendship::STATUS_ACCEPTED)
                        ->orderBy('u.username', 'ASC');

                    return $qb;
                },
                'choice_label' => 'username',
                'multiple' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'attr' => ['class' => 'form-select', 'data-live-search' => 'true'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
