<?php

namespace App\Controller;

use App\Entity\EventoCalendario;
use App\Repository\EventoCalendarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/calendario')]
class CalendarioController extends AbstractController
{
    // Esta es la ruta que Angular está buscando desesperadamente
    #[Route('', name: 'api_calendario_list', methods: ['GET'])]
    public function index(EventoCalendarioRepository $repo): JsonResponse
    {
        $dias = $repo->findAll(); 
        
        $data = [];
        foreach ($dias as $dia) {
            $data[] = [
                'id' => $dia->getId(),
                'dia' => $dia->getDia(),
                'juegos' => $dia->getJuegos(),
                'esFestivo' => $dia->isEsFestivo()
            ];
        }
        return $this->json($data);
    }

    // Esta ruta es para que tú (como Admin) puedas modificar los días
    #[Route('/{id}', name: 'api_calendario_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request, EventoCalendarioRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $diaCalendario = $repo->find($id);
        if (!$diaCalendario) {
            return $this->json(['error' => 'Día no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['juegos'])) {
            $diaCalendario->setJuegos($data['juegos']);
        }
        if (isset($data['esFestivo'])) {
            $diaCalendario->setEsFestivo((bool)$data['esFestivo']);
        }

        $em->flush();

        return $this->json(['message' => 'Calendario actualizado correctamente']);
    }
}