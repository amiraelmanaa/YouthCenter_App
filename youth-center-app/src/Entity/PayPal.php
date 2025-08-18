<?php

namespace App\Entity;

use App\Repository\PayPalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayPalRepository::class)]
class PayPal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booking $BookingID = null;

    #[ORM\Column(length: 255)]
    private ?string $emailpaypal = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse_de_facturation = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingID(): ?Booking
    {
        return $this->BookingID;
    }

    public function setBookingID(?Booking $BookingID): static
    {
        $this->BookingID = $BookingID;

        return $this;
    }

    public function getEmailpaypal(): ?string
    {
        return $this->emailpaypal;
    }

    public function setEmailpaypal(string $emailpaypal): static
    {
        $this->emailpaypal = $emailpaypal;

        return $this;
    }

    public function getAdresseDeFacturation(): ?string
    {
        return $this->Adresse_de_facturation;
    }

    public function setAdresseDeFacturation(string $Adresse_de_facturation): static
    {
        $this->Adresse_de_facturation = $Adresse_de_facturation;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

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
}
