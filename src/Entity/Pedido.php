<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
#[ORM\Table(name: 'pedidos')]
class Pedido
{
    // Definición de estados profesionales
    public const ESTADO_RESERVA = 'reserva';
    public const ESTADO_PENDIENTE_PAGO = 'pendiente_pago';
    public const ESTADO_COMPLETADO = 'completado';
    public const ESTADO_PREPARACION = 'en_preparacion';
    public const ESTADO_ENVIADO = 'enviado';
    public const ESTADO_CANCELADO = 'cancelado';
    public const ESTADO_DEVUELTO = 'devuelto';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['pedido:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups(['pedido:read'])]
    private ?string $numero_pedido = null;

    #[ORM\Column(length: 50)]
    #[Groups(['pedido:read'])]
    private ?string $estado = self::ESTADO_PENDIENTE_PAGO;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['pedido:read'])]
    private ?string $total = '0.00';

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: DetallePedido::class, cascade: ['persist', 'remove'])]
    #[Groups(['pedido:read'])]
    private Collection $detalles;

    #[ORM\Column]
    #[Groups(['pedido:read'])]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct() {
        $this->created_at = new \DateTimeImmutable();
        $this->detalles = new ArrayCollection();
        $this->numero_pedido = 'PED-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    // Getters y Setters básicos...
    public function getId(): ?int { return $this->id; }
    public function getNumeroPedido(): ?string { return $this->numero_pedido; }
    public function getEstado(): ?string { return $this->estado; }
    public function setEstado(string $estado): static { $this->estado = $estado; return $this; }
    public function getTotal(): ?string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }
    public function getUsuario(): ?Usuario { return $this->usuario; }
    public function setUsuario(?Usuario $usuario): static { $this->usuario = $usuario; return $this; }
    public function getDetalles(): Collection { return $this->detalles; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->created_at; }

    public function addDetalle(DetallePedido $detalle): static {
        if (!$this->detalles->contains($detalle)) {
            $this->detalles->add($detalle);
            $detalle->setPedido($this);
        }
        return $this;
    }
}
