<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository;
#[ORM\Entity(repositoryClass:CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idCommande = null ;


    #[ORM\Column(length:150)]
    private ? DateTime $orderDate = null;

    #[ORM\Column(length:150)]
    private ? string $status = null;

    #[ORM\Column(length:150)]
    private ? string $modePaiement = null;


    #[ORM\Column(length:150)]
    private ? string $adresseLivraison = null;


    #[ORM\Column(length:150)]
    private ? float $fraisLivraison = null;

    #[ORM\Column(length:150)]
    private ? float $total = null;


    #[ORM\ManyToOne(inversedBy:'Commandes')]
    private ?Panier $idPanier = null ;


    #[ORM\ManyToOne(inversedBy:'Commandes')]
    private ?User $userID = null ;



}
