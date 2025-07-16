<?php

namespace App\Entity;

use App\Repository\CenterManagerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CenterManagerRepository::class)]
class CenterManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'Manager_ID', cascade: ['persist', 'remove'])]
    private ?Center $center = null;

    #[ORM\Column]
    private ?int $nb_available_rooms = null;

    #[ORM\Column]
    private ?int $nb_booked_rooms = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

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

    public function getCenter(): ?Center
    {
        return $this->center;
    }

    public function setCenter(?Center $center): static
    {
        // unset the owning side of the relation if necessary
        if ($center === null && $this->center !== null) {
            $this->center->setManagerID(null);
        }

        // set the owning side of the relation if necessary
        if ($center !== null && $center->getManagerID() !== $this) {
            $center->setManagerID($this);
        }

        $this->center = $center;

        return $this;
    }
    public function __toString(): string
{
    return $this->name . ' ' . $this->lastname;
}

    public function getNbAvailableRooms(): ?int
    {
        return $this->nb_available_rooms;
    }

    public function setNbAvailableRooms(int $nb_available_rooms): static
    {
        $this->nb_available_rooms = $nb_available_rooms;

        return $this;
    }

    public function getNbBookedRooms(): ?int
    {
        return $this->nb_booked_rooms;
    }

    public function setNbBookedRooms(int $nb_booked_rooms): static
    {
        $this->nb_booked_rooms = $nb_booked_rooms;

        return $this;
    }

}
