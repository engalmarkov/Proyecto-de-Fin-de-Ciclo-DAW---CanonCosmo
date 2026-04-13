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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/pedidos')]
class PedidoController extends AbstractController
{
    /**
     * CREAR PEDIDO O RESERVA
     */
    #[Route('', name: 'api_pedido_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $em, ProductoRepository $productoRepo): JsonResponse
    {
        $usuario = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['items']) || empty($data['items'])) {
            return $this->json(['error' => 'El carrito está vacío'], 400);
        }

        $pedido = new Pedido();
        $pedido->setUsuario($usuario);
        
        // Si el frontend envía "es_reserva": true, tratamos el pedido como tal
        $esReserva = $data['es_reserva'] ?? false;
        $totalAcumulado = 0;

        foreach ($data['items'] as $item) {
            $producto = $productoRepo->find($item['producto_id']);
            if (!$producto) continue;

            // Lógica de Stock: Si no es reserva y no hay stock, error.
            if (!$esReserva && $producto->getStock() < $item['cantidad']) {
                return $this->json(['error' => "Stock insuficiente de {$producto->getNombre()}"], 400);
            }

            $detalle = new DetallePedido();
            $detalle->setProducto($producto);
            $detalle->setCantidad($item['cantidad']);
            $detalle->setPrecioUnitario($producto->getPrecio());
            
            $subtotal = $producto->getPrecio() * $item['cantidad'];
            $detalle->setSubtotal((string)$subtotal);
            $totalAcumulado += $subtotal;

            // SOLO restamos stock si NO es una reserva
            if (!$esReserva) {
                $producto->setStock($producto->getStock() - $item['cantidad']);
            }
            
            $pedido->addDetalle($detalle);
        }

        $pedido->setTotal((string)$totalAcumulado);
        $pedido->setEstado($esReserva ? Pedido::ESTADO_RESERVA : Pedido::ESTADO_PENDIENTE_PAGO);

        $em->persist($pedido);
        $em->flush();

        return $this->json([
            'message' => $esReserva ? 'Reserva creada' : 'Pedido pendiente de pago',
            'numero' => $pedido->getNumeroPedido(),
            'estado' => $pedido->getEstado()
        ], Response::HTTP_CREATED);
    }

    /**
     * CANCELAR PEDIDO (Devuelve el stock al inventario)
     */
    #[Route('/{id}/cancelar', name: 'api_pedido_cancel', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')] // El propio usuario o admin puede cancelar
    public function cancelar(Pedido $pedido, EntityManagerInterface $em): JsonResponse
    {
        // Si ya está enviado o cancelado, no se puede tocar
        if (in_array($pedido->getEstado(), [Pedido::ESTADO_CANCELADO, Pedido::ESTADO_ENVIADO])) {
            return $this->json(['error' => 'No se puede cancelar un pedido en este estado'], 400);
        }

        // LÓGICA PRO: Si el pedido restó stock (no era reserva), lo devolvemos
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

    /**
     * ADMIN: Cambiar a cualquier estado (Enviar, completar pago, etc)
     */
    #[Route('/{id}/estado', name: 'api_pedido_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cambiarEstado(Pedido $pedido, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $nuevoEstado = $data['nuevo_estado'] ?? null;

        if (!$nuevoEstado) return $this->json(['error' => 'Estado no proporcionado'], 400);

        $pedido->setEstado($nuevoEstado);
        $em->flush();

        return $this->json(['message' => "Estado actualizado a $nuevoEstado"]);
    }
    /**
     * LISTAR MIS PEDIDOS (Para el cliente)
     */
    #[Route('', name: 'api_pedidos_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(PedidoRepository $repo): JsonResponse
    {
        $usuario = $this->getUser();
        $pedidos = $repo->findBy(['usuario' => $usuario], ['created_at' => 'DESC']);

        // Symfony usará los @Groups(['pedido:read']) que pusimos en la Entidad
        return $this->json($pedidos, 200, [], ['groups' => 'pedido:read']);
    }
}