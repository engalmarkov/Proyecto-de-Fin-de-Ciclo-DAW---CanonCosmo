<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\PedidoRepository;
use App\Repository\ProductoRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin')] 
#[IsGranted('ROLE_ADMIN')]
class AdminWebController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(
        ProductoRepository $prodRepo, 
        PedidoRepository $pedRepo, 
        UsuarioRepository $userRepo
    ): JsonResponse { 
        $stats = [
            'total_productos' => $prodRepo->count(['activo' => true]),
            'total_pedidos'   => $pedRepo->count([]),
            'total_usuarios'  => $userRepo->count([]),
            'ventas_totales'  => $pedRepo->createQueryBuilder('p')
                ->select('SUM(p.total)')
                ->getQuery()
                ->getSingleScalarResult() ?? 0
        ];

        $ultimosPedidos = $pedRepo->findBy([], ['created_at' => 'DESC'], 5);

        return $this->json([
            'stats' => $stats,
            'pedidos' => $ultimosPedidos
        ]);
    }

    #[Route('/productos', name: 'admin_products', methods: ['GET'])]
    public function listProducts(ProductoRepository $repo): JsonResponse
    {
        return $this->json($repo->findBy([], ['id' => 'DESC']));
    }

    #[Route('/productos/guardar/{id}', name: 'admin_product_save', defaults: ['id' => null], methods: ['POST'])]
    public function save(
        ?Producto $producto, 
        Request $request, 
        EntityManagerInterface $em
    ): JsonResponse {
        if (!$producto) {
            $producto = new Producto();
        }

        $data = json_decode($request->getContent(), true);
        
        $producto->setNombre($data['nombre'] ?? $request->request->get('nombre'));
        $producto->setDescripcion($data['descripcion'] ?? $request->request->get('descripcion'));
        $producto->setPrecio($data['precio'] ?? $request->request->get('precio'));
        $producto->setStock($data['stock'] ?? $request->request->getInt('stock'));
        $producto->setCategoria($data['categoria'] ?? $request->request->get('categoria'));
        $producto->setImagenUrl($data['imagen_url'] ?? $request->request->get('imagen_url'));

        $em->persist($producto);
        $em->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'Artefacto guardado con éxito.',
            'producto' => $producto
        ]);
    }

    #[Route('/productos/eliminar/{id}', name: 'admin_product_delete', methods: ['DELETE'])]
    public function delete(Producto $producto, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($producto);
        $em->flush();
        
        return $this->json([
            'status' => 'success',
            'message' => 'Artefacto eliminado del multiverso.'
        ]);
    }
}