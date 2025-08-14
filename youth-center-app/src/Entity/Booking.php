<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?Room $room = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?\DateTime $startDate = null;

    #[ORM\Column]
    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 255)]
    private ?string $guestName = null;

    #[ORM\Column(length: 255)]
    private ?string $guestLastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;


    #[ORM\Column(nullable: true)]
    private ?int $guestsCount = null;

   #[ORM\Column(nullable: true)]
private ?float $totalPrice = null;

public function updateTotalPrice(): void
{
    if ($this->startDate && $this->endDate && $this->room) {
        $nights = $this->calculateNights($this->startDate, $this->endDate);
        $this->totalPrice = $nights * $this->room->getPricePerNight();
    } else {
        $this->totalPrice = null;
    }
}

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?bool $isGroupBooking = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Paid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;
        $this->updateTotalPrice();

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;
        $this->updateTotalPrice();

        return $this;
    }

    public function getGuestName(): ?string
    {
        return $this->guestName;
    }

    public function setGuestName(string $guestName): static
    {
        $this->guestName = $guestName;

        return $this;
    }

    public function getGuestsCount(): ?int
    {
        return $this->guestsCount;
    }

    public function setGuestsCount(?int $guestsCount): static
    {
        $this->guestsCount = $guestsCount;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isGroupBooking(): ?bool
    {
        return $this->isGroupBooking;
    }

    public function setIsGroupBooking(bool $isGroupBooking): static
    {
        $this->isGroupBooking = $isGroupBooking;

        return $this;
    }
    
    public function getGuestLastName(): ?string
    {
        return $this->guestLastName;
    }
    public function setGuestLastName(string $guestLastName): static
    {
        $this->guestLastName = $guestLastName;

        return $this;
    }
    public function getAge(): ?int
    {
        return $this->age;
    }
    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }
    public function calculateNights(\DateTime $startDate, \DateTime $endDate): int
    {
        return (int) $startDate->diff($endDate)->format('%a');
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->Paid;
    }

    public function setPaid(?bool $Paid): static
    {
        $this->Paid = $Paid;

        return $this;
    }
}
