<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subcategory $subcategory = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fabric $fabric = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?AdditionalService $additionalService = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order_ = null;

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

    public function getFabric(): ?Fabric
    {
        return $this->fabric;
    }

    public function setFabric(?Fabric $fabric): static
    {
        $this->fabric = $fabric;

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

    public function getAdditionalService(): ?AdditionalService
    {
        return $this->additionalService;
    }

    public function setAdditionalService(?AdditionalService $additionalService): static
    {
        $this->additionalService = $additionalService;

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
}
