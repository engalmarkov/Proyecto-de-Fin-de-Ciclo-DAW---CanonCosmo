<?php

namespace App\Controller;

use App\Entity\DetallePedido;
use App\Entity\Pedido;
use App\Repository\ProductoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/carrito')]
class CartController extends AbstractController
{
    #[Route('/', name: 'cart_index', methods: ['GET'])]
    public function index(SessionInterface $session, ProductoRepository $productoRepository): JsonResponse
    {
        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productoRepository->find($id);
            if ($product) {
                $subtotal = $product->getPrecio() * $quantity;
                $cartData[] = [
                    'id' => $product->getId(),
                    'nombre' => $product->getNombre(),
                    'precio' => $product->getPrecio(),
                    'imagen_url' => $product->getImagenUrl(),
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        return $this->json([
            'items' => $cartData,
            'total' => $total
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(int $id, SessionInterface $session, Request $request): JsonResponse
    {
        $cart = $session->get('cart', []);
        
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? $request->request->getInt('quantity', 1);
        
        if ($quantity < 1) $quantity = 1;

        if (!isset($cart[$id])) {
            $cart[$id] = 0;
        }
        $cart[$id] += $quantity;

        $session->set('cart', $cart);

        return $this->json([
            'status' => 'success',
            'message' => 'Inventario actualizado correctamente.',
            'cartCount' => count($cart)
        ]);
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['DELETE'])] 
    public function remove(int $id, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        
        return $this->json([
            'status' => 'success',
            'message' => 'Producto eliminado del carrito.'
        ]);
    }

    #[Route('/checkout', name: 'cart_checkout', methods: ['POST'])]
    public function checkout(
        SessionInterface $session, 
        ProductoRepository $productoRepository,
        EntityManagerInterface $em
    ): JsonResponse 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cart = $session->get('cart', []);
        if (empty($cart)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Tu carrito está vacío.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $pedido = new Pedido();
        $pedido->setUsuario($this->getUser());
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productoRepository->find($id);
            if ($product) {
                $detalle = new DetallePedido();
                $detalle->setProducto($product);
                $detalle->setCantidad($quantity);
                $detalle->setPrecioUnitario($product->getPrecio());
                $detalle->setSubtotal($product->getPrecio() * $quantity);
                
                $pedido->addDetalle($detalle);
                $total += $detalle->getSubtotal();
            }
        }

        $pedido->setTotal((string)$total);
        $pedido->setEstado(Pedido::ESTADO_PENDIENTE_PAGO);

        $em->persist($pedido);
        $em->flush();

        $session->remove('cart');

        return $this->json([
            'status' => 'success',
            'message' => 'Pedido realizado con éxito.',
            'pedidoId' => $pedido->getId()
        ], JsonResponse::HTTP_CREATED);
    }
}