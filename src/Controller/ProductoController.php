<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductoController extends AbstractController
{
    // ─── PÚBLICO: listar catálogo con BUSCADOR ──────────────────────────────
    #[Route('/api/productos', name: 'api_productos_list', methods: ['GET'])]
    public function index(ProductoRepository $repo, Request $request): JsonResponse
    {
        // 1. Recogemos los filtros de la URL (ej: ?buscar=luffy&precio_max=20)
        $nombre = $request->query->get('buscar');
        $maxPrecio = $request->query->get('precio_max');

        // 2. Creamos la consulta base (solo productos activos)
        $query = $repo->createQueryBuilder('p')
            ->where('p.activo = :activo')
            ->setParameter('activo', true);

        // 3. Aplicamos filtros si existen
        if ($nombre) {
            $query->andWhere('p.nombre LIKE :nombre')
                  ->setParameter('nombre', '%'.$nombre.'%');
        }

        if ($maxPrecio) {
            $query->andWhere('p.precio <= :max')
                  ->setParameter('max', $maxPrecio);
        }

        $productos = $query->orderBy('p.id', 'DESC')->getQuery()->getResult();

        // 4. Mapeamos los datos para que el JSON sea limpio
        $data = array_map(fn(Producto $p) => [
            'id'          => $p->getId(),
            'nombre'      => $p->getNombre(),
            'categoria'   => $p->getCategoria(),
            'descripcion' => $p->getDescripcion(),
            'precio'      => $p->getPrecio(),
            'stock'       => $p->getStock(),
            'imagen_url'  => $p->getImagenUrl(),
        ], $productos);

        return $this->json(['productos' => $data, 'total' => count($data)]);
    }

    // ─── PÚBLICO: ver detalle de un producto ──────────────────────────────
    #[Route('/api/productos/{id}', name: 'api_productos_show', methods: ['GET'])]
    public function show(int $id, ProductoRepository $repo): JsonResponse
    {
        $producto = $repo->find($id);

        if (!$producto || !$producto->isActivo()) {
            return $this->json(['error' => 'Producto no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id'          => $producto->getId(),
            'nombre'      => $producto->getNombre(),
            'categoria'   => $producto->getCategoria(),
            'descripcion' => $producto->getDescripcion(),
            'precio'      => $producto->getPrecio(),
            'stock'       => $producto->getStock(),
            'imagen_url'  => $producto->getImagenUrl(),
            'createdAt'   => $producto->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    // ─── PROTEGIDO (ROLE_ADMIN): crear producto ───────────────────────────
    #[Route('/api/admin/productos', name: 'api_admin_productos_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre'], $data['precio'])) {
            return $this->json([
                'error' => 'Los campos nombre y precio son obligatorios.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setCategoria($data['categoria'] ?? 'General');
        $producto->setDescripcion($data['descripcion'] ?? null);
        $producto->setPrecio((string) $data['precio']);
        $producto->setStock($data['stock'] ?? 0);
        $producto->setImagenUrl($data['imagen_url'] ?? null);

        $errors = $validator->validate($producto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($producto);
        $em->flush();

        return $this->json([
            'message'  => 'Producto creado correctamente.',
            'producto' => [
                'id'          => $producto->getId(),
                'nombre'      => $producto->getNombre(),
                'categoria'   => $producto->getCategoria(),
                'precio'      => $producto->getPrecio(),
                'createdAt'   => $producto->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ], Response::HTTP_CREATED);
    }

    // ─── PROTEGIDO (ROLE_ADMIN): desactivar producto (borrado lógico) ─────
    #[Route('/api/admin/productos/{id}', name: 'api_admin_productos_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, ProductoRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $producto = $repo->find($id);
        if (!$producto) {
            return $this->json(['error' => 'Producto no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $producto->setActivo(false);
        $em->flush();

        return $this->json(['message' => 'Producto desactivado correctamente.']);
    }
}