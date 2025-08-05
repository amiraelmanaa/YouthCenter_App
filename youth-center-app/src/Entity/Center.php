<?php

namespace App\Entity;

use App\Repository\CenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Activities;

#[ORM\Entity(repositoryClass: CenterRepository::class)]
class Center
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $country;

    #[ORM\Column(length: 255)]
    private string $city;

    #[ORM\Column(length: 255)]
    private string $address;

    #[ORM\Column(length: 255)]
    private string $category;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    /**
     * @var Collection<int, Activities>
     */
    #[ORM\ManyToMany(targetEntity: Activities::class, mappedBy: 'centers', cascade: ['persist'])]
    private Collection $activities;

    #[ORM\OneToOne(inversedBy: 'center', cascade: ['persist', 'remove'])]
    private ?CenterManager $Manager_ID = null;

    #[ORM\Column]
    private ?int $nb_rooms = null;

    /**
     * @var Collection<int, Pictures>
     */
    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'Center_ID')]
    private Collection $pictures;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'center')]
    private Collection $Rooms;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->Rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;   }
    public function getCountry(): string
    {
        return $this->country;
}
 public function getPhone(): ?string
                                                                            {
                                                                                return $this->phone;
                                                                            }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
     public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

  public function setDescription(?string $description): self
{
 
    $description = strip_tags($description);

    
    $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');


    $this->description = $description;

    return $this;
}
        public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }
     public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return Collection<int, Activities>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activities $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->addCenter($this);
        }

        return $this;
    }

    public function removeActivity(Activities $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removeCenter($this);
        }

        return $this;
    }

    public function getManagerID(): ?CenterManager
    {
        return $this->Manager_ID;
    }

    public function setManagerID(?CenterManager $Manager_ID): static
    {
        $this->Manager_ID = $Manager_ID;

        return $this;
    }

    public function getNbRooms(): ?int
    {
        return $this->nb_rooms;
    }

    public function setNbRooms(int $nb_rooms): static
    {
        $this->nb_rooms = $nb_rooms;

        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Pictures $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setCenterID($this);
        }

        return $this;
    }

    public function removePicture(Pictures $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            
            if ($picture->getCenterID() === $this) {
                $picture->setCenterID(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
}

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->Rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->Rooms->contains($room)) {
            $this->Rooms->add($room);
            $room->setCenter($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->Rooms->removeElement($room)) {
            if ($room->getCenter() === $this) {
                $room->setCenter(null);
            }
        }

        return $this;
    }


}
