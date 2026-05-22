<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/api/novedades', name: 'app_novedades')] // Cambiar la ruta a algo descriptivo
    public function index(ProductoRepository $productoRepository): JsonResponse //
    {
        // Obtenemos los últimos 4 productos activos
        $productosNovedades = $productoRepository->findBy(
            ['activo' => true],
            ['id' => 'DESC'],
            4
        );

        // Devolvemos solo el array con los datos
        return $this->json([
            'novedades' => $productosNovedades,
        ]);
    }
}