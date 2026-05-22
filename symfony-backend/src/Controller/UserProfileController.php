<?php

namespace App\Controller;

use App\Repository\PedidoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    #[Route('/api/perfil', name: 'app_user_profile', methods: ['GET', 'OPTIONS'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(PedidoRepository $pedidoRepository): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Ahora sí buscará los pedidos porque el Repositorio ya existe
        $pedidos = $pedidoRepository->findBy(
            ['usuario' => $user],
            ['created_at' => 'DESC'] // Usamos exactamente el nombre de tu variable
        );

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'nombre' => $user->getNombre(),
                'apellidos' => $user->getApellidos(),
            ],
            'pedidos' => $pedidos
        ], 200, [], ['groups' => 'pedido:read']); 
    }
}