<?php

namespace App\Entity;

use App\Repository\ServiceCoefficientsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceCoefficientsRepository::class)]
class ServiceCoefficients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $expressCoefficient = null;

    #[ORM\Column]
    private ?float $delicateCoefficient = null;

    #[ORM\Column]
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

    public function getDelicateCoefficient(): ?float
    {
        return $this->delicateCoefficient;
    }

    public function setDelicateCoefficient(float $delicateCoefficient): static
    {
        $this->delicateCoefficient = $delicateCoefficient;

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
