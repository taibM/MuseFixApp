<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PackRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PackRepository::class)]
class Pack
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // Ajout de cette ligne pour empêcher la génération automatique de l'identifiant
    #[ORM\Column]
    public ?int $id = null;   

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"le type  de pack est requis.")]
    #[Assert\Regex(
        pattern: '/^\D+$/',
        message: "Le type de pack ne doit pas contenir de nombres."
    )]
    protected ?string $type_pack= null; 

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message:" le prix est  requis")] 
    #[Assert\GreaterThan(value: 0, message: "Le prix doit être supérieur à zéro.")]
    #[Assert\Positive(message:"Le prix doit être un nombre positif.")]
    private ?float $prix = null; 
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "l'avantage est requis")]
    #[Assert\Regex(
        pattern: '/^\D+$/',
        message: "Le type de pack ne doit pas contenir de nombres."
    )]
    private ?string $avantage = null;


    public function __toString(): string
    {
        return $this->type_pack; 
    }

    public function getIdpack(): ?int
    {
        return $this->id;
    }

    public function getTypePack(): ?string
    {
        return $this->type_pack;
    }

    public function setTypePack(string $type_pack): static
    {
        $this->type_pack = $type_pack;

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