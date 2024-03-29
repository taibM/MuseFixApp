<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use DateTime;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $userid = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-zA-Z]+$/')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-zA-Z]+$/')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/')]
    private ?string $passwd = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual("today")]
    private ?DateTime $signupdate = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    private ?int $role = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^\d{8}$/')]
    private ?string $tel = null;

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswd(): ?string
    {
        return $this->passwd;
    }

    public function setPasswd(string $passwd): static
    {
        $this->passwd = $passwd;

        return $this;
    }

    public function getSignupdate(): ?\DateTimeInterface
    {
        return $this->signupdate;
    }

    public function setSignupdate(\DateTime $signupdate): static
    {
        $this->signupdate = $signupdate;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }


}
