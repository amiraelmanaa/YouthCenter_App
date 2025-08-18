<?php

namespace App\Entity;

use App\Repository\CarteBancaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarteBancaireRepository::class)]
class CarteBancaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_du_titulaire = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_de_carte = null;

    #[ORM\Column(length: 255)]
    private ?string $date_dexpiration = null;

    #[ORM\Column(length: 255)]
    private ?string $code_cvv = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $Ville = null;

    #[ORM\Column(length: 255)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booking $BookingId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDuTitulaire(): ?string
    {
        return $this->nom_du_titulaire;
    }

    public function setNomDuTitulaire(string $nom_du_titulaire): static
    {
        $this->nom_du_titulaire = $nom_du_titulaire;

        return $this;
    }

    public function getNumeroDeCarte(): ?string
    {
        return $this->numero_de_carte;
    }

    public function setNumeroDeCarte(string $numero_de_carte): static
    {
        $this->numero_de_carte = $numero_de_carte;

        return $this;
    }

    public function getDateDexpiration(): ?string
    {
        return $this->date_dexpiration;
    }

    public function setDateDexpiration(string $date_dexpiration): static
    {
        $this->date_dexpiration = $date_dexpiration;

        return $this;
    }

    public function getCodeCvv(): ?string
    {
        return $this->code_cvv;
    }

    public function setCodeCvv(string $code_cvv): static
    {
        $this->code_cvv = $code_cvv;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): static
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): static
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getBookingId(): ?Booking
    {
        return $this->BookingId;
    }

    public function setBookingId(?Booking $BookingId): static
    {
        $this->BookingId = $BookingId;

        return $this;
    }
}
