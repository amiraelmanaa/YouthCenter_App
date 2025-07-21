<?php

namespace App\Entity;

use App\Repository\ActivitiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivitiesRepository::class)]
class Activities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $decription = null;

    /**
     * @var Collection<int, Center>
     */
    #[ORM\ManyToMany(targetEntity: Center::class, inversedBy: 'activities')]
    private Collection $centers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    public function __construct()
    {
        $this->centers = new ArrayCollection();
    }


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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDecription(): ?string
    {
        return $this->decription;
    }

    public function setDecription(?string $decription): static
    {
        $this->decription = $decription;

        return $this;
    }

    /**
     * @return Collection<int, Center>
     */
    public function getCenters(): Collection
    {
        return $this->centers;
    }

    public function addCenter(Center $centerId): static
    {
        if (!$this->centers->contains($centerId)) {
            $this->centers->add($centerId);
        }

        return $this;
    }

    public function removeCenter(Center $centerId): static
    {
        $this->centers->removeElement($centerId);

        return $this;
    }

public function __toString(): string
{
    // Return a string representation of your activity
    return $this->getName() ?? 'Activity #'.$this->getId();
    // Or if you have a 'title' field: return $this->getTitle();
}

public function getType(): ?string
{
    return $this->type;
}

public function setType(?string $type): static
{
    $this->type = $type;

    return $this;
}


}
