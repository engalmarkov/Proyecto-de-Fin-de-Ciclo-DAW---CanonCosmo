<?php

namespace App\Entity;

use App\Repository\EventoCalendarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventoCalendarioRepository::class)]
class EventoCalendario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $dia = null;

    #[ORM\Column(length: 255)]
    private ?string $juegos = null;

    #[ORM\Column]
    private ?bool $esFestivo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDia(): ?string
    {
        return $this->dia;
    }

    public function setDia(string $dia): static
    {
        $this->dia = $dia;
        return $this;
    }

    public function getJuegos(): ?string
    {
        return $this->juegos;
    }

    public function setJuegos(string $juegos): static
    {
        $this->juegos = $juegos;
        return $this;
    }

    public function isEsFestivo(): ?bool
    {
        return $this->esFestivo;
    }

    public function setEsFestivo(bool $esFestivo): static
    {
        $this->esFestivo = $esFestivo;
        return $this;
    }
}