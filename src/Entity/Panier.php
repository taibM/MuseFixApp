<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idPanier")]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"la quantité est requise.")]
    #[Assert\Positive(message:"La quantité doit être un nombre positif.")]
    private ?int $qte = null;

    #[ORM\Column(name: "prixUnite")]
    #[Assert\NotBlank(message:"le prix unitaire est requis.")]
    #[Assert\Positive(message:"Le prix unitaire doit être un nombre positif.")]
    private ?float $prixUnite = null;

    #[ORM\Column(name: "sousTotal")]
    #[Assert\NotBlank(message:"le sous-total est requis.")]
    #[Assert\Positive(message:"Le sous-total doit être un nombre positif.")]
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


    /**
     * Calculer le sous-total du panier pour cet article.
     */
    public function calculerSousTotal(): float
    {
        return $this->qte * $this->prixUnite;
    }

    /**
     * Mettre à jour le sous-total du panier.
     */
    public function mettreAJourSousTotal(): void
    {
        $this->sousTotal = $this->calculerSousTotal();
    }

    /**
     * Calculer le total du panier.
     *
     * @param Collection|Panier[] $paniers
     * @return float
     */
    public static function calculerTotal(Collection $paniers): float
    {
        $total = 0;
        foreach ($paniers as $panier) {
            $total += $panier->getSousTotal();
        }
        return $total;
    }

    /**
     * Mettre à jour le sous-total et le total du panier.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function mettreAJourTotals(): void
    {
        $this->mettreAJourSousTotal();
        // Vous pouvez également mettre à jour le total du panier ici
        // en utilisant la méthode calculerTotal si nécessaire.
    }

    /**
     * Mettre à jour le sous-total lorsque l'entité est chargée.
     *
     * @ORM\PostLoad
     */
    public function mettreAJourSousTotalOnLoad(): void
    {
        $this->mettreAJourSousTotal();
    }
}
