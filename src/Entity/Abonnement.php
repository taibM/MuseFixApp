<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\repository\abonnementRepository;
#[ORM\Entity(repositoryClass:abonnementRepository::class)]
class abonnement
{
   #[ORM\Id]
   #[ORM\GeneratedValue]
   #[ORM\Column]
   private ?int $idAbonnement = null ;   
 
#[ORM\Column(length:150)]
private ? date $dateDeb=null; 
    
#[ORM\Column(length:150)]
private ? date $dateFin=null; 
           
   
   #[ORM\ManyToOne(inversedBy:'abonnement')]
     private ?User $userID = null ; 
      
     #[ORM\ManyToOne(inversedBy:'abonnement')]
     private ?Pack $idPack = null ; 
   

    public function getIdabonnement(): ?int
    {
        return $this->idabonnement;
    }

    public function getDatedeb(): ?\DateTimeInterface
    {
        return $this->datedeb;
    }

    public function setDatedeb(\DateTimeInterface $datedeb): static
    {
        $this->datedeb = $datedeb;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

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

    public function getIdpack(): ?Pack
    {
        return $this->idpack;
    }

    public function setIdpack(?Pack $idpack): static
    {
        $this->idpack = $idpack;

        return $this;
    }


}
