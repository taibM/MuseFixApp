<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idCommande")]
    private ?int $id = null;

    #[ORM\Column(name: 'orderDate', type: 'datetime')]
    private ?\DateTimeInterface $orderDate = null;


    #[ORM\Column(name: 'status', type: "enum", options: ["livree", "annule", "enCours"])]
    private ?string $status = null;

    #[ORM\Column(name: 'modePaiement', type: "enum", options: ["carte", "liquide", "paypal"])]
    private ?string $modePaiement = null;

    #[ORM\Column(name: 'adresseLivraison', type: "string", length: 255)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(name: 'fraisLivraison', type: "float")]
    private ?float $fraisLivraison = null;

    #[ORM\Column(name: 'total', type: "float")]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: "commandes")]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "id")]
    private ?User $userid = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getFraisLivraison(): ?float
    {
        return $this->fraisLivraison;
    }

    public function setFraisLivraison(?float $fraisLivraison): static
    {
        $this->fraisLivraison = $fraisLivraison;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): static
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

    public function getIdPanier(): ?Panier
    {
        return $this->idPanier;
    }

    public function setIdPanier(?Panier $idPanier): static
    {
        $this->idPanier = $idPanier;

        return $this;
    }
}
