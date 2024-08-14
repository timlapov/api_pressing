<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\OrderStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
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
            securityMessage: "Only administrators can create new order statuses."
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify order statuses."
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can modify order statuses."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Only administrators can delete order statuses."
        ),
    ],
    normalizationContext: ['groups' => ['order_status:read']],
    denormalizationContext: ['groups' => ['order_status:write']]
)]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order_status:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Status name is required")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Status name must be at least {{ limit }} characters long",
        maxMessage: "Status name cannot be longer than {{ limit }} characters"
    )]
    #[Groups(['order_status:read', 'order_status:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'orderStatus')]
    private Collection $orders;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['order_status:read', 'order_status:write'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setOrderStatus($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOrderStatus() === $this) {
                $order->setOrderStatus(null);
            }
        }

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
}
