<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AbonnementRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idAbonnement = null;

    #[ORM\Column(length: 150)]
    private ?\DateTimeInterface $datedeb = null;

    #[ORM\Column(length: 150)]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\ManyToOne(inversedBy: 'abonnement')]
    private ?User $userID = null;

    #[ORM\ManyToOne(inversedBy: 'abonnement')]
    private ?Pack $idPack = null;

    public function getIdAbonnement(): ?int
    {
        return $this->idAbonnement;
    }

    public function getDatedeb(): ?\DateTimeInterface
    {
        return $this->datedeb;
    }

    public function setDatedeb(\DateTimeInterface $datedeb): self
    {
        $this->datedeb = $datedeb;
        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;
        return $this;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): self
    {
        $this->userID = $userID;
        return $this;
    }

    public function getIdPack(): ?Pack
    {
        return $this->idPack;
    }

    public function setIdPack(?Pack $idPack): self
    {
        $this->idPack = $idPack;
        return $this;
    }
}