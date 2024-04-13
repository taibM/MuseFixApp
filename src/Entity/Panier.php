<?php

namespace App\Entity;


use App\Repository\PanierRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idPanier")]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $qte = null;


    #[ORM\Column(name: "prixUnite")]
    private ?float $prixUnite = null;

    #[ORM\Column(name: "sousTotal")]
    private ?float $sousTotal = null;


    #[ORM\ManyToOne(inversedBy: 'paniers')]
    #[ORM\JoinColumn(name: "userID", referencedColumnName: "id")]
    private ?User $userid = null;


    #[ORM\ManyToOne(inversedBy: "paniers")]
    #[ORM\JoinColumn(name: "idProduit", referencedColumnName: "id")]
    private ?Produit $idproduit = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getqte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPrixUnite(): ?float
    {
        return $this->prixUnite;
    }

    public function setPrixUnite(float $prixUnite): static
    {
        $this->prixUnite = $prixUnite;

        return $this;
    }

    public function getSousTotal(): ?float
    {
        return $this->sousTotal;
    }

    public function setSousTotal(float $sousTotal): static
    {
        $this->sousTotal = $sousTotal;

        return $this;
    }



    public function getIdproduit(): ?Produit
    {
        return $this->idproduit;
    }

    public function setIdproduit(?Produit $idproduit): static
    {
        $this->idproduit = $idproduit;

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


}
