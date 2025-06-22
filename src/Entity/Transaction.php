<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\Column(type: 'uuid')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $transactionId;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 4)]
    private string $amount;

    #[ORM\Column(length: 255)]
    private string $currency;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private Ledger $ledger;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): static
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function setLedger(Ledger $ledger): static
    {
        $this->ledger = $ledger;
        return $this;
    }
}
