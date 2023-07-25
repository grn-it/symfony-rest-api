<?php

declare(strict_types=1);

namespace App\Entity;

use App\Component\Exception\EntityPropertyNotSetException;
use App\Repository\PaymentRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api', 'api_admin'])]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['api', 'api_admin', 'exchange'])]
    private int $amount = 0;

    #[ORM\Column(type: Types::GUID, nullable: true)]
    #[Groups(['exchange'])]
    private ?string $uuid = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api', 'api_admin', 'exchange'])]
    private ?PaymentStatus $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $ord = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentType $type = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api', 'api_admin', 'exchange'])]
    private ?DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['api_admin', 'exchange'])]
    private ?DateTime $updatedAt = null;

    public function getId(): int
    {
        if (!$this->id) {
            throw new EntityPropertyNotSetException('Property "id" must be set.');
        }
        
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUuid(): string
    {
        if (!$this->uuid) {
            throw new EntityPropertyNotSetException('Property "uuid" must be set.');
        }
        
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getStatus(): PaymentStatus
    {
        if (!$this->status) {
            throw new EntityPropertyNotSetException('Property "status" must be set.');
        }
        
        return $this->status;
    }

    public function setStatus(PaymentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrder(): Order
    {
        if (!$this->ord) {
            throw new EntityPropertyNotSetException('Property "order" must be set.');
        }

        return $this->ord;
    }

    public function setOrder(Order $order): self
    {
        $this->ord = $order;

        return $this;
    }

    public function getType(): PaymentType
    {
        if (!$this->type) {
            throw new EntityPropertyNotSetException('Property "type" must be set.');
        }
        
        return $this->type;
    }

    public function setType(?PaymentType $type): self
    {
        $this->type = $type;

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
