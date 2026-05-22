<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Entity\DetallePedido;
use App\Entity\Producto;
use App\Repository\PedidoRepository;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/pedidos')]
class PedidoController extends AbstractController
{
    #[Route('', name: 'api_pedido_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $em, ProductoRepository $productoRepo): JsonResponse
    {
        $usuario = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['items']) || empty($data['items'])) {
            return $this->json(['error' => 'El carrito está vacío'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $pedido = new Pedido();
        $pedido->setUsuario($usuario);

        $esReserva = $data['es_reserva'] ?? false;
        $totalAcumulado = 0;

        foreach ($data['items'] as $item) {
            $producto = $productoRepo->find($item['producto_id']);
            if (!$producto)
                continue;

            if (!$esReserva && $producto->getStock() < $item['cantidad']) {
                return $this->json(['error' => "Stock insuficiente de {$producto->getNombre()}"], JsonResponse::HTTP_BAD_REQUEST);
            }

            $detalle = new DetallePedido();
            $detalle->setProducto($producto);
            $detalle->setCantidad($item['cantidad']);
            $detalle->setPrecioUnitario($producto->getPrecio());

            $subtotal = $producto->getPrecio() * $item['cantidad'];
            $detalle->setSubtotal((string) $subtotal);
            $totalAcumulado += $subtotal;

            if (!$esReserva) {
                $producto->setStock($producto->getStock() - $item['cantidad']);
            }

            $pedido->addDetalle($detalle);
        }

        $pedido->setTotal((string) $totalAcumulado);
        $pedido->setEstado($esReserva ? Pedido::ESTADO_RESERVA : Pedido::ESTADO_PENDIENTE_PAGO);

        $em->persist($pedido);
        $em->flush();

        return $this->json([
            'message' => $esReserva ? 'Reserva creada' : 'Pedido pendiente de pago',
            'numero' => $pedido->getNumeroPedido(),
            'estado' => $pedido->getEstado()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}/cancelar', name: 'api_pedido_cancel', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function cancelar(Pedido $pedido, EntityManagerInterface $em): JsonResponse
    {
        if (in_array($pedido->getEstado(), [Pedido::ESTADO_CANCELADO, Pedido::ESTADO_ENVIADO])) {
            return $this->json(['error' => 'No se puede cancelar un pedido en este estado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($pedido->getEstado() !== Pedido::ESTADO_RESERVA) {
            foreach ($pedido->getDetalles() as $detalle) {
                $producto = $detalle->getProducto();
                $producto->setStock($producto->getStock() + $detalle->getCantidad());
            }
        }

        $pedido->setEstado(Pedido::ESTADO_CANCELADO);
        $em->flush();

        return $this->json(['message' => 'Pedido cancelado y stock devuelto al inventario.']);
    }

    #[Route('/{id}/estado', name: 'api_pedido_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cambiarEstado(Pedido $pedido, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nuevoEstado = $data['nuevo_estado'] ?? null;

        if (!$nuevoEstado) {
            return $this->json(['error' => 'Estado no proporcionado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $pedido->setEstado($nuevoEstado);
        $em->flush();

        return $this->json(['message' => "Estado actualizado a $nuevoEstado"]);
    }

    #[Route('', name: 'api_pedidos_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(PedidoRepository $repo): JsonResponse
    {
        $usuario = $this->getUser();
        $pedidos = $repo->findBy(['usuario' => $usuario], ['created_at' => 'DESC']);

        return $this->json($pedidos, JsonResponse::HTTP_OK, [], ['groups' => 'pedido:read']);
    }

    #[Route('/{id}', name: 'api_pedido_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(int $id, PedidoRepository $repo): JsonResponse
    {
        $pedido = $repo->find($id);

        if (!$pedido) {
            return $this->json(['error' => 'Pedido no encontrado'], 404);
        }

        // Usamos context para asegurar que el serializador sea profundo
        return $this->json($pedido, 200, [], [
            'groups' => ['pedido:read'],
            'circular_reference_handler' => function ($object) {
                return $object->getId(); }
        ]);
    }
}