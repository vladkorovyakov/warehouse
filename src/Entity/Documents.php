<?php

namespace App\Entity;

use App\Repository\DocumentsRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsRepository::class)]
#[ORM\Index(name: 'created_idx', columns: ['created'])]
#[ORM\Index(name: 'u_type_created_idx', columns: ['type', 'created'])]
class Documents
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $productId = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $created = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\OneToOne(mappedBy: 'document', cascade: ['persist', 'remove'])]
    private ?InventoryErrors $inventoryError = null;

    #[ORM\OneToOne(mappedBy: 'document', cascade: ['persist', 'remove'])]
    private ?PricePerProduct $pricePerProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface $created): static
    {
        $this->created = $created;

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

    public function getInventoryError(): ?InventoryErrors
    {
        return $this->inventoryError;
    }

    public function setInventoryError(InventoryErrors $inventoryError): static
    {
        // set the owning side of the relation if necessary
        if ($inventoryError->getDocument() !== $this) {
            $inventoryError->setDocument($this);
        }

        $this->inventoryError = $inventoryError;

        return $this;
    }

    public function getPricePerProduct(): ?PricePerProduct
    {
        return $this->pricePerProduct;
    }

    public function setPricePerProduct(PricePerProduct $pricePerProduct): static
    {
        // set the owning side of the relation if necessary
        if ($pricePerProduct->getDocument() !== $this) {
            $pricePerProduct->setDocument($this);
        }

        $this->pricePerProduct = $pricePerProduct;

        return $this;
    }
}
