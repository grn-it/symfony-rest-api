<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\Exception\EntityPropertyNotSetException;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1000)] // phpcs:ignore
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(type: Types::GUID, nullable: true)]
    private ?string $uuid = null;

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
            throw new EntityPropertyNotSetException('Property "product" name must be set.');
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
