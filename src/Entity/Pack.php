<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PackRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PackRepository::class)]
class Pack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPack = null;   

    #[ORM\Column(length: 255)]
    private ?string $typePack = null; 

    #[ORM\Column(type: 'float')]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $avantage = null;

    public function getIdpack(): ?int
    {
        return $this->idpack;
    }

    public function getTypepack(): ?string
    {
        return $this->typepack;
    }

    public function setTypepack(string $typepack): static
    {
        $this->typepack = $typepack;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAvantage(): ?string
    {
        return $this->avantage;
    }

    public function setAvantage(string $avantage): static
    {
        $this->avantage = $avantage;

        return $this;
    }


}
