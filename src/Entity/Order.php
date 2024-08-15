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
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can view the list of all orders."
        ),
        new Get(
            security: "is_granted('ROLE_EMPLOYEE') or (is_granted('ROLE_USER') and object.getClient() == user)",
            securityMessage: "You can only view your own orders or you need to be an employee."
        ),
        new Post(
            denormalizationContext: ['groups' => ['order:write'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Only clients can create new orders.",
            validationContext: ['groups' => ['Default', 'order:write']]
        ),
        new Put(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can modify the orders."
        ),
        new Patch(
            security: "is_granted('ROLE_EMPLOYEE')",
            securityMessage: "Only employees can modify the orders."
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getOrder().getClient() == user and object.getOrder().getOrderStatus().getName() == 'CREATED'",
            securityMessage: "Only administrators can delete orders."
        ),
    ],
    normalizationContext: ['groups' => ['order:read']],
    denormalizationContext: ['groups' => ['order:write']]
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Order status is required")]
    #[Groups(['order:read', 'order:write'])]
//    #[ApiProperty(readableLink: true, writableLink: false)]
    private ?OrderStatus $orderStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['order:read'])]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['order:read', 'order:write'])]
    private ?\DateTimeInterface $completed = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Client is required")]
    #[Groups(['order:read', 'order:write'])]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['order:read', 'order:write'])]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'order_', cascade: ['persist', 'remove'])]
    #[Groups(['order:read', 'order:write'])]
    #[ApiProperty(readableLink: true, writableLink: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->created = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getCompleted(): ?\DateTimeInterface
    {
        return $this->completed;
    }

    public function setCompleted(\DateTimeInterface $completed): static
    {
        $this->completed = $completed;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

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
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

//    #[Groups(['order:read'])]
//    public function getTotalPrice(): float
//    {
//        $total = 0;
//        foreach ($this->items as $item) {
//            $price = $item->getService()->getPrice();
//            $price *= $item->getSubcategory()->getPriceCoefficient();
//            $price *= $item->getFabric()->getPriceCoefficient();
//            foreach ($item->getAdditionalServices() as $additionalService) {
//                $price *= $additionalService->getPriceCoefficient();
//            }
//            $total += $price;
//        }
//        return $total;
//    }

    #[Groups(['order:read'])]
    public function getTotalPrice(): float
    {
        if ($this->items->isEmpty()) {
            return 0.0;
        }

        return array_sum($this->items->map(fn(Item $item) => $item->getCalculatedPrice())->toArray());
    }
}
