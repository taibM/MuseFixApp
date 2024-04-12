<?php

namespace App\Entity;


use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(type: "enum", options: ["livree", "annule", "enCours"])]
    private ?string $status = null;

    #[ORM\Column(type: "enum", options: ["carte", "liquide", "paypal"])]
    private ?string $modePaiement = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type: "float")]
    private ?float $fraisLivraison = null;

    #[ORM\Column(type: "float")]
    private ?float $total = null;


    #[ORM\ManyToOne(inversedBy: "commandes")]
    private ?User $userid = null;

    #[ORM\ManyToOne(inversedBy: "commandes")]
    private ?Panier $idpanier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    public function setModePaiement($modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getFraisLivraison(): ?float
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(float $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getIdpanier(): ?Panier
    {
        return $this->idpanier;
    }

    public function setIdpanier(?Panier $idpanier): static
    {
        $this->idpanier = $idpanier;

        return $this;
    }



}
