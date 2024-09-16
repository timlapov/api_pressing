<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ServiceCoefficientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ServiceCoefficientRepository::class)]
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
    normalizationContext: ['groups' => ['service_coefficient:read']],
    denormalizationContext: ['groups' => ['service_coefficient:write']]
)]
class ServiceCoefficient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service_coefficient:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['service_coefficient:read'])]
    private ?float $expressCoefficient = null;

    #[ORM\Column]
    #[Groups(['service_coefficient:read'])]
    private ?float $ironingCoefficient = null;

    #[ORM\Column]
    #[Groups(['service_coefficient:read'])]
    private ?float $perfumingCoefficient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpressCoefficient(): ?float
    {
        return $this->expressCoefficient;
    }

    public function setExpressCoefficient(float $expressCoefficient): static
    {
        $this->expressCoefficient = $expressCoefficient;

        return $this;
    }

    public function getIroningCoefficient(): ?float
    {
        return $this->ironingCoefficient;
    }

    public function setIroningCoefficient(float $ironingCoefficient): static
    {
        $this->ironingCoefficient = $ironingCoefficient;

        return $this;
    }

    public function getPerfumingCoefficient(): ?float
    {
        return $this->perfumingCoefficient;
    }

    public function setPerfumingCoefficient(float $perfumingCoefficient): static
    {
        $this->perfumingCoefficient = $perfumingCoefficient;

        return $this;
    }
}
