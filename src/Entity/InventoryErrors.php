<?php

namespace App\Entity;

use App\Repository\InventoryErrorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventoryErrorsRepository::class)]
class InventoryErrors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'inventoryError', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Documents $document = null;

    #[ORM\Column]
    private ?int $errorValue = null;

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

    public function getErrorValue(): ?int
    {
        return $this->errorValue;
    }

    public function setErrorValue(int $errorValue): static
    {
        $this->errorValue = $errorValue;

        return $this;
    }
}
