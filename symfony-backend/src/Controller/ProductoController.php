<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Categoria;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductoController extends AbstractController
{
    // ─── PÚBLICO: listar catálogo con BUSCADOR Y FILTRO ──────────────────────────────
    #[Route('/api/productos', name: 'api_productos_list', methods: ['GET'])]
    public function index(ProductoRepository $repo, Request $request): JsonResponse
    {
        $nombre = $request->query->get('buscar');
        $maxPrecio = $request->query->get('precio_max');
        $categoria = $request->query->get('categoria');

        $query = $repo->createQueryBuilder('p')
            ->leftJoin('p.categoria', 'c')
            ->where('p.activo = :activo')
            ->setParameter('activo', true);

        if ($categoria && $categoria !== 'Todos') {
            $query->andWhere('c.nombre = :categoriaNombre')
                ->setParameter('categoriaNombre', $categoria);
        }

        if ($nombre) {
            $query->andWhere('p.nombre LIKE :nombre')
                ->setParameter('nombre', '%' . $nombre . '%');
        }

        if ($maxPrecio) {
            $query->andWhere('p.precio <= :max')
                ->setParameter('max', $maxPrecio);
        }

        $productos = $query->orderBy('p.id', 'DESC')->getQuery()->getResult();

        $data = array_map(fn(Producto $p) => [
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'categoria' => $p->getCategoria() ? [
                'id' => $p->getCategoria()->getId(),
                'nombre' => $p->getCategoria()->getNombre()
            ] : null,
            'descripcion' => $p->getDescripcion(),
            'precio' => $p->getPrecio(),
            'precioOferta' => $p->getPrecioOferta(),
            'stock' => $p->getStock(),
            'imagen_url' => $p->getImagenUrl(),
        ], $productos);

        return $this->json(['productos' => $data, 'total' => count($data)]);
    }

    // ─── PÚBLICO: ver detalle de un producto ──────────────────────────────
    #[Route('/api/productos/{id}', name: 'api_productos_show', methods: ['GET'])]
    public function show(int $id, ProductoRepository $repo): JsonResponse
    {
        $producto = $repo->find($id);

        if (!$producto || !$producto->isActivo()) {
            return $this->json([
                'error' => 'Producto no encontrado en este sector.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $producto->getId(),
            'nombre' => $producto->getNombre(),
            'categoria' => $producto->getCategoria() ? [
                'id' => $producto->getCategoria()->getId(),
                'nombre' => $producto->getCategoria()->getNombre()
            ] : null,
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'precioOferta' => $producto->getPrecioOferta(),
            'stock' => $producto->getStock(),
            'imagen_url' => $producto->getImagenUrl(),
            'createdAt' => $producto->getCreatedAt() ? $producto->getCreatedAt()->format('Y-m-d H:i:s') : null,
        ]);
    }

    // ─── PROTEGIDO: crear producto ───────────────────────────
    #[Route('/api/admin/productos', name: 'api_admin_productos_create', methods: ['POST'])]
    // #[IsGranted('ROLE_ADMIN')] // Comentado para desarrollo
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        CategoriaRepository $categoriaRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nombre'], $data['precio'])) {
            return $this->json([
                'error' => 'Los campos nombre y precio son obligatorios para registrar el producto.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setDescripcion($data['descripcion'] ?? null);
        $producto->setPrecio((string) $data['precio']);

        if (isset($data['precioOferta'])) {
            $producto->setPrecioOferta((float) $data['precioOferta']);
        }

        $producto->setStock($data['stock'] ?? 0);
        $producto->setImagenUrl($data['imagen_url'] ?? null);

        // Asignamos la categoría recibiendo el ID de Angular (por defecto usa el 1)
// Asignamos la categoría recibiendo el ID de Angular (por defecto usa el 1)
        $categoriaId = $data['categoria_id'] ?? 1;
        $categoria = $categoriaRepo->find($categoriaId);

        // ¡LA SOLUCIÓN! Si la categoría no existe en la BD, forjamos una de emergencia
        if (!$categoria) {
            $categoria = new Categoria();
            $categoria->setNombre('Sin Categoría'); // o 'General', 'Miscelánea'...
            $em->persist($categoria);
            // Forzamos el guardado de la categoría para que genere un ID antes de seguir
            $em->flush();
        }

        // Ahora sí, 100% seguros de que tenemos una categoría válida
        $producto->setCategoria($categoria);

        // VALORES POR DEFECTO PARA EVITAR ERRORES SQL
        if (method_exists($producto, 'setActivo')) {
            $producto->setActivo(true);
        }
        if (method_exists($producto, 'setCreatedAt') && !$producto->getCreatedAt()) {
            $producto->setCreatedAt(new \DateTimeImmutable());
        }

        $errors = $validator->validate($producto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($producto);

        // INTENTAMOS GUARDAR ATRAPANDO EL ERROR REAL
        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->json([
                'error_real' => 'El servidor falló al guardar en BD: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'Producto forjado correctamente.',
            'producto' => [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio' => $producto->getPrecio(),
            ]
        ], JsonResponse::HTTP_CREATED);
    }

    // ─── PROTEGIDO: actualizar producto (PUT) ───────────────────────────
    #[Route('/api/admin/productos/{id}', name: 'api_admin_productos_update', methods: ['PUT'])]
    // #[IsGranted('ROLE_ADMIN')] // Comentado para desarrollo
    public function update(
        int $id,
        Request $request,
        ProductoRepository $repo,
        EntityManagerInterface $em,
        CategoriaRepository $categoriaRepo,
        ValidatorInterface $validator
    ): JsonResponse {
        $producto = $repo->find($id);

        if (!$producto) {
            return $this->json(['error' => 'No se ha encontrado el objeto para actualizar.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Actualizamos solo los campos que vengan en la petición
        if (isset($data['nombre'])) {
            $producto->setNombre($data['nombre']);
        }
        if (isset($data['descripcion'])) {
            $producto->setDescripcion($data['descripcion']);
        }
        if (isset($data['precio'])) {
            $producto->setPrecio((string) $data['precio']);
        }
        if (isset($data['precioOferta'])) {
            $producto->setPrecioOferta((float) $data['precioOferta']);
        }
        if (isset($data['stock'])) {
            $producto->setStock($data['stock']);
        }
        if (isset($data['imagen_url'])) {
            $producto->setImagenUrl($data['imagen_url']);
        }
if (isset($data['categoria_id'])) {
            $categoria = $categoriaRepo->find($data['categoria_id']);
            if (!$categoria) {
                return $this->json(['error' => 'La categoría indicada no existe.'], 400);
            }
            $producto->setCategoria($categoria);
        }

        $errors = $validator->validate($producto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // INTENTAMOS ACTUALIZAR ATRAPANDO EL ERROR REAL
        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->json([
                'error_real' => 'El servidor falló al actualizar en BD: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Objeto actualizado con éxito en el inventario.']);
    }

    // ─── PROTEGIDO: borrar producto (Desactivar) ───────────────────────────
    #[Route('/api/admin/productos/{id}', name: 'api_admin_productos_delete', methods: ['DELETE'])]
    // #[IsGranted('ROLE_ADMIN')] // Comentado para desarrollo
    public function delete(int $id, ProductoRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $producto = $repo->find($id);
        if (!$producto) {
            return $this->json([
                'error' => 'No se puede eliminar lo que no existe.'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $producto->setActivo(false);
        $em->flush();

        return $this->json([
            'message' => 'Producto eliminado (desactivado) del catálogo.'
        ]);
    }
}