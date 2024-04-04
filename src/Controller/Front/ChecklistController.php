<?php

namespace App\Controller\Front;

use App\Entity\Checklist;
use App\Form\ChecklistType;
use App\Repository\ChecklistRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/checklist')]
class ChecklistController extends AbstractController
{
    #[Route('/', name: 'app_checklist_index', methods: ['GET'])]
    public function index(ChecklistRepository $checklistRepository): Response
    {
        return $this->render('front/checklist/index.html.twig', [
            'checklists' => $checklistRepository->findAll(),
        ]);
    }

    #[Route('/checklist/new', name: 'app_checklist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TripRepository $tripRepository): Response
    {
        $checklist = new Checklist();
        $user = $this->getUser();
        $checklist->setParticipant($user);
        $checklist->setCreatedAt(new \DateTimeImmutable());

        // Récupérer tripId comme un paramètre de requête
        $tripId = $request->query->get('id');

        if (null !== $tripId) {
            $trip = $tripRepository->find($tripId);
            if (!$trip) {
                throw $this->createNotFoundException(sprintf('Le voyage avec l\'ID "%s" n\'existe pas.', $tripId));
            }
            $checklist->setTrip($trip);
            $checklist->setProperty('Publique');
        } else {
            $checklist->setProperty('Privé');
        }

        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter la date et l'heure de création automatiquement
            $checklist->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($checklist);
            $entityManager->flush();

            // Redirection conditionnelle après la soumission du formulaire
            if (null !== $checklist->getTrip()) {
                // Rediriger vers la liste des checklists du voyage si associé à un voyage
                return $this->redirectToRoute('app_trip_checklists', ['id' => $checklist->getTrip()->getId()]);
            } else {
                // Sinon, rediriger vers une route générale (par exemple, la liste des checklists privées)
                $urlPrecedente = $request->headers->get('referer');

                return new RedirectResponse($urlPrecedente);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Tâche ajoutée avec succès.']);
        } else {
            // Gérer la redirection pour les requêtes non-AJAX

            if ($request->isXmlHttpRequest()) {
                // Si le formulaire n'est pas valide, retourner les erreurs sous forme JSON
                $errors = []; // Vous pouvez implémenter la logique d'extraction des erreurs ici

                return new JsonResponse(['success' => false, 'errors' => $errors]);
            } else {
                // Affichage du formulaire pour une requête GET normale ou POST non valide
                return $this->render('front/checklist/new.html.twig', [
                    'checklist' => $checklist,
                    'form' => $form->createView(),
                ]);
            }
        }
    }

    #[Route('/{id}', name: 'app_checklist_show', methods: ['GET'])]
    public function show(Checklist $checklist): Response
    {
        return $this->render('front/checklist/show.html.twig', [
            'checklist' => $checklist,
        ]);
    }

    // TODO : corriger le probléme de "Unknown column 't0.is_verified' in 'field list" qui survient uniquement dans le edit de chaque entité
    #[Route('/{id}/edit', name: 'app_checklist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Checklist $checklist, EntityManagerInterface $entityManager, TripRepository $tripRepository): Response
    {
        $form = $this->createForm(ChecklistType::class, $checklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Vérifier la propriété de la checklist après soumission du formulaire
            if ('Privé' === $checklist->getProperty()) {
                // Si la checklist est privée, rediriger vers la route générale des checklists
                return $this->redirectToRoute('app_main_checklist', [], Response::HTTP_SEE_OTHER);
            } else {
                // Si la checklist est publique, obtenir le trip associé et rediriger vers la route spécifique du voyage
                $trip = $checklist->getTrip();
                if (!$trip) {
                    throw $this->createNotFoundException('Le voyage associé n\'existe pas.');
                }

                return $this->redirectToRoute('app_trip_checklists', ['id' => $trip->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('front/checklist/edit.html.twig', [
            'checklist' => $checklist,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_checklist_delete', methods: ['POST'])]
    public function delete(Request $request, Checklist $checklist, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($checklist);
        $entityManager->flush();

        return $this->redirectToRoute('app_checklist_index');
    }
}
