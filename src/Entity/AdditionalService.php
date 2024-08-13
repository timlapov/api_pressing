<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AdditionalServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdditionalServiceRepository::class)]
#[ApiResource]
class AdditionalService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name is required')]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price_coefficient = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'additionalService')]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPriceCoefficient(): ?float
    {
        return $this->price_coefficient;
    }

    public function setPriceCoefficient(float $price_coefficient): static
    {
        $this->price_coefficient = $price_coefficient;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setAdditionalService($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getAdditionalService() === $this) {
                $item->setAdditionalService(null);
            }
        }

        return $this;
    }
}
