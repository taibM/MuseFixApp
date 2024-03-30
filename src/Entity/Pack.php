<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


use App\repository\abonnementRepository;
#[ORM\Entity(repositoryClass:packRepository::class)]
class pack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPack = null;   

   
    #[ORM\Column(Length:255)]
    private ?string $typePack=null; 
    


    
    #[ORM\Column]
    private ? float $prix=null;


    #[ORM\Column(Length:255)]
    private ?string $avantage=null;

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
