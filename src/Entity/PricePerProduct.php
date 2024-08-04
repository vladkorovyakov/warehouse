<?php

namespace App\Entity;

use App\Repository\PricePerProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PricePerProductRepository::class)]
class PricePerProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'pricePerProduct', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Documents $document = null;

    #[ORM\Column]
    private ?int $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Documents
    {
        return $this->document;
    }

    public function setDocument(Documents $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }
}
