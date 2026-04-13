<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Repository\PedidoRepository;
use App\Repository\ProductoRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/api/admin/stats', name: 'api_admin_stats', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getStats(
        ProductoRepository $pRepo, 
        PedidoRepository $oRepo, 
        UsuarioRepository $uRepo
    ): JsonResponse {
        
        // Calculamos los ingresos totales sumando la columna 'total' de los pedidos completados
        $ingresos = $oRepo->createQueryBuilder('p')
            ->select('SUM(p.total)')
            ->where('p.estado = :status')
            ->setParameter('status', Pedido::ESTADO_COMPLETADO)
            ->getQuery()
            ->getSingleScalarResult();

        return $this->json([
            'total_productos' => $pRepo->count([]),
            'productos_sin_stock' => $pRepo->count(['stock' => 0]),
            'total_pedidos' => $oRepo->count([]),
            'ingresos_totales' => $ingresos ?? 0,
            'total_usuarios' => $uRepo->count([])
        ]);
    }
}