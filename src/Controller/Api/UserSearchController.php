<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserSearchController extends AbstractController
{
    #[NoReturn] #[Route('/api/users/search', name: 'api_users_search')]
    public function search(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->query->get('q', '');

        // La méthode `findByNameOrUsername` est à implémenter dans UserRepository
        $users = $userRepository->findByNameOrUsername($query);

        // Transformez les résultats en tableau JSON-friendly
        $data = array_map(function ($user) {
            return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            ];
        }, $users);

        return $this->json(['users' => $data]);
    }
}
