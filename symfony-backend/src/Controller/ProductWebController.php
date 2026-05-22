<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/productos-web')]
class ProductWebController extends AbstractController
{
    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductoRepository $repo, Request $request): JsonResponse
    {
        $categoria = $request->query->get('categoria');
        $buscar = $request->query->get('buscar');

        $query = $repo->createQueryBuilder('p')
            ->where('p.activo = :activo')
            ->setParameter('activo', true);

        if ($categoria && $categoria !== 'Todos') {
            $query->andWhere('p.categoria = :cat')
                  ->setParameter('cat', $categoria);
        }

        if ($buscar) {
            $query->andWhere('p.nombre LIKE :buscar OR p.descripcion LIKE :buscar')
                  ->setParameter('buscar', '%'.$buscar.'%');
        }

        $productos = $query->orderBy('p.id', 'DESC')->getQuery()->getResult();

        return $this->json([
            'products' => $productos
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(?Producto $producto): JsonResponse 
    {
        if (!$producto || !$producto->isActivo()) {
            return $this->json([
                'error' => 'El producto solicitado no existe o no está disponible.'
            ], JsonResponse::HTTP_NOT_FOUND); // 404
        }

        return $this->json($producto);
    }
}