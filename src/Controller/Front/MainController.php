<?php

namespace App\Controller\Front;

use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_home')]
    public function index(): Response
    {
        return $this->render('front/main/index.html.twig', [
            'controller_name' => 'MainController',
            'isUserLoggedIn' => null !== $this->getUser(),
        ]);
    }

    #[Route('/mentions', name: 'app_main_mentions')]
    public function mentions(): Response
    {
        return $this->render('front/main/mentions.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/faq', name: 'app_main_faq')]
    public function faq(): Response
    {
        return $this->render('front/main/faq.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/contact', name: 'app_main_contact')]
    public function contact(EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter la date et l'heure de création automatiquement

            // Persiste l'article en base de données.
            $entityManager->persist($form->getData());
            $entityManager->flush();

            // Redirige vers la liste des articles après la création réussie.
            return $this->redirectToRoute('app_main_home');
        }

        // Affiche le formulaire de création d'article.
        return $this->render('front/main/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
