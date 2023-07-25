<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\Exception\EntityPropertyNotSetException;
use App\Repository\OrderProductRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1000)] // phpcs:ignore
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[SerializedName('order')]
    private ?Order $ord = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $updatedAt = null;

    public function getId(): int
    {
        if (!$this->id) {
            throw new EntityPropertyNotSetException('Property "id" must be set.');
        }

        return $this->id;
    }

    public function getName(): string
    {
        if (!$this->name) {
            throw new EntityPropertyNotSetException('Property "name" must be set.');
        }

        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        if (!$this->description) {
            throw new EntityPropertyNotSetException('Property "description" must be set.');
        }

        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): int
    {
        if (!$this->price) {
            throw new EntityPropertyNotSetException('Property "price" must be set.');
        }

        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOrder(): Order
    {
        if (!$this->ord) {
            throw new EntityPropertyNotSetException('Property "order" must be set.');
        }

        return $this->ord;
    }

    public function setOrder(?Order $order): self
    {
        $this->ord = $order;

        return $this;
    }

    public function getOrd(): Order
    {
        if (!$this->ord) {
            throw new EntityPropertyNotSetException('Property "order" must be set.');
        }

        return $this->ord;
    }

    public function setOrd(?Order $order): self
    {
        $this->ord = $order;

        return $this;
    }
    
    public function getQuantity(): int
    {
        if (!$this->quantity) {
            throw new EntityPropertyNotSetException('Property "quantity" must be set.');
        }

        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): Product
    {
        if (!$this->product) {
            throw new EntityPropertyNotSetException('Property "product" must be set.');
        }

        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
