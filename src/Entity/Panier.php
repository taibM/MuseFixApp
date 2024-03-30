<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PanierRepository;
#[ORM\Entity(repositoryClass:PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idPanier = null ;

    #[ORM\Column(length:150)]
    private ? int $qte = null;

    #[ORM\Column(length:150)]
    private ? float $prixUnite = null;

    #[ORM\Column(length:150)]
    private ? float $sousTotal = null;


    #[ORM\ManyToOne(inversedBy:'paniers')]
    private ?User $userID = null ;


    #[ORM\ManyToOne(inversedBy:'paniers')]
    private ?Produit $idProduit = null ;

    public function getIdpanier(): ?int
    {
        return $this->idpanier;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPrixunite(): ?float
    {
        return $this->prixunite;
    }

    public function setPrixunite(float $prixunite): static
    {
        $this->prixunite = $prixunite;

        return $this;
    }

    public function getSoustotal(): ?float
    {
        return $this->soustotal;
    }

    public function setSoustotal(float $soustotal): static
    {
        $this->soustotal = $soustotal;

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

    public function getIdproduit(): ?Produit
    {
        return $this->idproduit;
    }

    public function setIdproduit(?Produit $idproduit): static
    {
        $this->idproduit = $idproduit;

        return $this;
    }


}
