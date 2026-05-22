<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
#[ORM\Table(name: 'productos')]
class Producto
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['producto:read', 'pedido:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre no puede estar vacío.')]
    #[Groups(['producto:read', 'producto:write', 'pedido:read'])]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['producto:read', 'producto:write'])]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'El precio debe ser positivo.')]
    #[Groups(['producto:read', 'producto:write', 'pedido:read'])]
    private ?string $precio = null;

    #[ORM\Column]
    #[Assert\NotNull, Assert\PositiveOrZero]
    #[Groups(['producto:read', 'producto:write'])]
    private ?int $stock = 0;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['producto:read', 'producto:write', 'pedido:read'])]
    private ?string $imagenUrl = null;

    #[ORM\Column]
    #[Groups(['producto:read'])]
    private bool $activo = true;

    #[ORM\Column]
    #[Groups(['producto:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Categoria::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['producto:read', 'producto:write'])]
    private ?Categoria $categoria = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['producto:read', 'producto:write', 'pedido:read'])]
    private ?float $precioOferta = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- GETTERS Y SETTERS ---
    public function getId(): ?int { return $this->id; }
    
    public function getNombre(): ?string { return $this->nombre; }
    public function setNombre(string $nombre): static { $this->nombre = $nombre; return $this; }
    
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function setDescripcion(?string $descripcion): static { $this->descripcion = $descripcion; return $this; }
    
    public function getPrecio(): ?string { return $this->precio; }
    public function setPrecio(string $precio): static { $this->precio = $precio; return $this; }
    
    public function getStock(): ?int { return $this->stock; }
    public function setStock(int $stock): static { $this->stock = $stock; return $this; }
    
    public function getImagenUrl(): ?string { return $this->imagenUrl; }
    public function setImagenUrl(?string $imagenUrl): static { $this->imagenUrl = $imagenUrl; return $this; }
    
    public function isActivo(): bool { return $this->activo; }
    public function setActivo(bool $activo): static { $this->activo = $activo; return $this; }
    
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getCategoria(): ?Categoria { return $this->categoria; }
    public function setCategoria(?Categoria $categoria): static { $this->categoria = $categoria; return $this; }

    public function getPrecioOferta(): ?float { return $this->precioOferta; }
    public function setPrecioOferta(?float $precioOferta): static { $this->precioOferta = $precioOferta; return $this; }
}