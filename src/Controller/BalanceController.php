<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/balances')]
final class BalanceController extends AbstractController
{
    #[Route('/{ledgerId}', name: 'get_balance', methods: ['GET'])]
    public function getBalance(string $ledgerId, EntityManagerInterface $em): JsonResponse
    {
        $ledger = $em->getRepository(Ledger::class)->find($ledgerId);
        if (!$ledger) {
            return $this->json(['error' => 'Ledger not found'], 404);
        }

        $transactions = $ledger->getTransactions();

        $balances = [];
        foreach ($transactions as $transaction) {
            $currency = $transaction->getCurrency();
            $amount = (float)$transaction->getAmount();
            $type = $transaction->getType();

            if (!isset($balances[$currency])) {
                $balances[$currency] = 0.0;
            }

            if ($type === 'credit') {
                $balances[$currency] += $amount;
            } elseif ($type === 'debit') {
                $balances[$currency] -= $amount;
            }
        }

        return $this->json($balances);
    }
}
