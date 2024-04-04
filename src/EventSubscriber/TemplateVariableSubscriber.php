<?php

namespace App\EventSubscriber;

use App\Form\ChecklistType;
use App\Repository\ChecklistRepository;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as TwigEnvironment;

#[\AllowDynamicProperties] class TemplateVariableSubscriber implements EventSubscriberInterface
{
    public function __construct(TwigEnvironment $twig, Security $security, FriendshipRepository $friendshipRepository, ChecklistRepository $checklistRepository, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->friendshipRepository = $friendshipRepository;
        $this->checklistRepository = $checklistRepository;
        $this->formFactory = $formFactory;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $user = $this->security->getUser();
        // Ajoute la variable 'user' globalement à tous les templates Twig
        $this->twig->addGlobal('user', $user);

        if ($user) {
            // Récupérer les amis acceptés de l'utilisateur
            $acceptedFriendships = $this->friendshipRepository->findAcceptedFriendshipsByUser($user);

            // Ajoutez la variable 'acceptedFriendships' globalement à tous les templates Twig
            $this->twig->addGlobal('acceptedFriendships', $acceptedFriendships);

            $checklists = $this->checklistRepository->findByUser($user);
            // Ajoutez la variable 'checklists' globalement à tous les templates Twig
            $this->twig->addGlobal('checklists', $checklists);

            $privateChecklists = $this->checklistRepository->findBy([
                'participant' => $user,
                'property' => 'Privé',
            ]);
            $this->twig->addGlobal('checklists', $privateChecklists);

            $formChecklist = $this->formFactory->create(ChecklistType::class);
            $this->twig->addGlobal('formChecklist', $formChecklist->createView());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
