<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'detalles_pedido')]
class DetallePedido
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pedido::class, inversedBy: 'detalles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedido $pedido = null;

    #[ORM\ManyToOne(targetEntity: Producto::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['pedido:read'])]
    private ?Producto $producto = null;

    #[ORM\Column]
    #[Groups(['pedido:read'])]
    private ?int $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['pedido:read'])]
    private ?string $precio_unitario = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['pedido:read'])]
    private ?string $subtotal = null;

    public function getId(): ?int { return $this->id; }
    public function setPedido(?Pedido $pedido): static { $this->pedido = $pedido; return $this; }
    public function getProducto(): ?Producto { return $this->producto; }
    public function setProducto(?Producto $producto): static { $this->producto = $producto; return $this; }
    public function getCantidad(): ?int { return $this->cantidad; }
    public function setCantidad(int $cantidad): static { $this->cantidad = $cantidad; return $this; }
    public function getPrecioUnitario(): ?string { return $this->precio_unitario; }
    public function setPrecioUnitario(string $precio_unitario): static { $this->precio_unitario = $precio_unitario; return $this; }
    public function getSubtotal(): ?string { return $this->subtotal; }
    public function setSubtotal(string $subtotal): static { $this->subtotal = $subtotal; return $this; }
}