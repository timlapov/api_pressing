<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\FabricRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FabricRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can create new fabrics."
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify fabrics."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify fabrics."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can delete fabrics."
        ),
    ],
    normalizationContext: ['groups' => ['fabric:read']],
    denormalizationContext: ['groups' => ['fabric:write']]
)]
class Fabric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fabric:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'fabric name is required')]
    #[Groups(['fabric:read', 'fabric:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['fabric:read', 'fabric:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Price coefficient is required")]
    #[Assert\Range(
        notInRangeMessage: "Price coefficient must be between {{ min }} and {{ max }}",
        min: 0.1,
        max: 10
    )]
    #[Groups(['fabric:read', 'fabric:write'])]
    private ?float $priceCoefficient = null;

//    /**
//     * @var Collection<int, Item>
//     */
//    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'fabric')]
//    private Collection $items;
//
//    public function __construct()
//    {
//        $this->items = new ArrayCollection();
//    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceCoefficient(): ?float
    {
        return $this->priceCoefficient;
    }

    public function setPriceCoefficient(float $priceCoefficient): static
    {
        $this->priceCoefficient = $priceCoefficient;

        return $this;
    }

//    /**
//     * @return Collection<int, Item>
//     */
//    public function getItems(): Collection
//    {
//        return $this->items;
//    }
//
//    public function addItem(Item $item): static
//    {
//        if (!$this->items->contains($item)) {
//            $this->items->add($item);
//            $item->setFabric($this);
//        }
//
//        return $this;
//    }
//
//    public function removeItem(Item $item): static
//    {
//        if ($this->items->removeElement($item)) {
//            // set the owning side to null (unless already changed)
//            if ($item->getFabric() === $this) {
//                $item->setFabric(null);
//            }
//        }
//
//        return $this;
//    }
}
