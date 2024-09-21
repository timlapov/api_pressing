<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\SubcategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubcategoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: false,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can create new subcategories."
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify subcategories."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify subcategories."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can delete subcategories."
        ),
    ],
    normalizationContext: ['groups' => ['subcategory:read']],
    denormalizationContext: ['groups' => ['subcategory:write']]
)]
class Subcategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['subcategory:read', 'client:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'subcategory name is required')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Subcategory name must be at least {{ limit }} characters long",
        maxMessage: "Subcategory name cannot be longer than {{ limit }} characters"
    )]
    #[Groups(['subcategory:read', 'subcategory:write', 'client:read', 'employee:read', 'order:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Price coefficient is required")]
    #[Assert\Positive(message: "Price coefficient must be a positive number")]
    #[Groups(['subcategory:read', 'subcategory:write', 'client:read'])]
    private ?float $price_coefficient = null;

    #[ORM\ManyToOne(inversedBy: 'subcategories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Category is required")]
    #[Groups(['subcategory:read', 'subcategory:write', 'client:read', 'employee:read'])]
    private ?Category $category = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'subcategory')]
    private Collection $items;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['subcategory:read', 'subcategory:write'])]
    private ?string $imageUrl = null;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

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
            $item->setSubcategory($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getSubcategory() === $this) {
                $item->setSubcategory(null);
            }
        }

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
