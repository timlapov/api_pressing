<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can view the list of items."
        ),
        new Get(
            security: "is_granted('ROLE_EMPLOYEE') or (is_granted('ROLE_USER') and object.getOrder().getClient() == user)",
            securityMessage: "You can only view items from your own orders or you need to be an employee."
        ),
        new Post(
            security: "is_granted('ROLE_USER') or is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only clients or employees can create new items."
        ),
        new Put(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can modify items."
        ),
        new Patch(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can modify items."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can delete items."
        ),
    ],
    normalizationContext: ['groups' => ['item:read']],
    denormalizationContext: ['groups' => ['item:write']]
)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:read', 'client:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
//    #[Groups(['item:read', 'item:write'])]
    #[ApiProperty(readableLink: true, writableLink: true)]
    #[Groups(['order:read', 'order:write', 'client:read', 'employee:read'])]
    private ?Subcategory $subcategory = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Service is required")]
//    #[Groups(['item:read', 'item:write'])]
    #[Groups(['order:read', 'order:write', 'client:read', 'employee:read'])]
    #[ApiProperty(readableLink: true, writableLink: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Order is required")]
    #[Groups(['item:read', 'item:write'])]
    private ?Order $order_ = null;

    #[ORM\Column]
    #[Groups(['item:read', 'order:write', 'client:read', 'employee:read', 'order:read'])]
    private ?bool $ironing = false;

    #[ORM\Column]
    #[Groups(['item:read', 'order:write', 'client:read', 'employee:read', 'order:read'])]
    private ?bool $perfuming = false;

    #[ORM\Column]
    #[Groups(['item:read', 'item:write', 'order:read', 'client:read'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['item:read', 'client:read', 'employee:read', 'order:read'])]
    private ?float $subcategoryCoefficient = null;

    #[ORM\Column]
    #[Groups(['item:read', 'order:write', 'client:read', 'employee:read', 'order:read'])]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): static
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order_;
    }

    public function setOrder(?Order $order_): static
    {
        $this->order_ = $order_;

        return $this;
    }

    #[Groups(['item:read'])]
    public function getCalculatedPrice(): float
    {
        if ($this->service === null) {
            return 0.0;
        }

        $price = $this->service->getPrice();

        // Use the stored subcategory coefficient
        $price *= $this->subcategoryCoefficient ?? $this->subcategory->getPriceCoefficient();

        $coefficients = $this->getOrder()->getServiceCoefficients();

        if (empty($coefficients)) {
            throw new \Exception('Service coefficients are not set in the order.');
        }

        if ($this->ironing) {
            $ironingCoefficient = $coefficients['ironingCoefficient'] ?? 1.0;
            $price *= $ironingCoefficient;
        }

        if ($this->perfuming) {
            $perfumingCoefficient = $coefficients['perfumingCoefficient'] ?? 1.0;
            $price *= $perfumingCoefficient;
        }

        // Multiply by quantity
        $price *= $this->quantity;

        return $price;
    }

    public function isIroning(): ?bool
    {
        return $this->ironing;
    }

    public function setIroning(bool $ironing): static
    {
        $this->ironing = $ironing;

        return $this;
    }

    public function isPerfuming(): ?bool
    {
        return $this->perfuming;
    }

    public function setPerfuming(bool $perfuming): static
    {
        $this->perfuming = $perfuming;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSubcategoryCoefficient(): ?float
    {
        return $this->subcategoryCoefficient;
    }

    public function setSubcategoryCoefficient(float $subcategoryCoefficient): self
    {
        $this->subcategoryCoefficient = $subcategoryCoefficient;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

}
