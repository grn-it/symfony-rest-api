<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\Exception\EntityPropertyNotSetException;
use App\Repository\OrderStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): int
    {
        if (!$this->id) {
            throw new EntityPropertyNotSetException('Property "id" must be set.');
        }

        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
