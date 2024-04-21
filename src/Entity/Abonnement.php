<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AbonnementRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use DateTime;
#[Assert\Callback('validateDates')]
#[ORM\Entity(repositoryClass:AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idAbonnement = null;

    #[ORM\Column(type:'datetime')]
    #[Assert\NotBlank(message:"la date de fin  est requis.")]
    private ?DateTime  $datedeb = null;

    #[ORM\Column(type:'datetime')]
    #[Assert\NotBlank(message:"le  date de début  est requis.")]
    private ?DateTime $datefin = null;
   
    #[ORM\ManyToOne(inversedBy: 'abonnement')]
    #[Assert\NotBlank(message:"le userId est requis.")]
    private ?User $userID = null;

    #[ORM\ManyToOne(inversedBy: 'abonnement')]
    #[Assert\NotBlank(message:"l'id pack est requis.")]
    private ?Pack $idPack = null;

    public function getIdAbonnement(): ?int
    {
        return $this->idAbonnement;
    }

    public function getDatedeb():?DateTime
    {
        return $this->datedeb;
    }

    public function setDatedeb(DateTime  $datedeb): self
    {
        $this->datedeb = $datedeb;
        return $this;
    }

    public function getDatefin(): ?DateTime
    {
        return $this->datefin;
    }

    public function setDatefin(DateTime $datefin): self
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
    
   
    public function validateDates(ExecutionContextInterface $context)
    {
        if ($this->datedeb >= $this->datefin) {
            $context->buildViolation('La date de début doit être inférieure à la date de fin.')
                ->atPath('datedeb')
                ->addViolation();
        }
    }

}