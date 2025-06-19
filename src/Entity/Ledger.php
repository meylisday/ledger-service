<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LedgerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use App\Entity\Transaction;

#[ORM\Entity(repositoryClass: LedgerRepository::class)]
class Ledger
{
    #[ORM\Id]
    #[ORM\GeneratedValue('CUSTOM')]
    #[ORM\Column(type: 'uuid')]
    #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    private ?UuidInterface $id = null;

    #[ORM\Column(length: 3, nullable: false)]
    private string $baseCurrency;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'ledger')]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): static
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}