<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="userID", columns={"userID"}), @ORM\Index(name="idPanier", columns={"idPanier"})})
 * @ORM\Entity
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="idCommande", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcommande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDate", type="datetime", nullable=false)
     */
    private $orderdate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="modePaiement", type="string", length=0, nullable=false)
     */
    private $modepaiement;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseLivraison", type="string", length=255, nullable=false)
     */
    private $adresselivraison;

    /**
     * @var float
     *
     * @ORM\Column(name="fraisLivraison", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraislivraison;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=false)
     */
    private $total;

    /**
     * @var \Panier
     *
     * @ORM\ManyToOne(targetEntity="Panier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPanier", referencedColumnName="idPanier")
     * })
     */
    private $idpanier;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;


}
