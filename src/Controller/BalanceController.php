<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/balances')]
final class BalanceController extends AbstractController
{
    #[Route('/{ledgerId}', name: 'get_balance', methods: ['GET'])]
    #[OA\Get(
        summary: "Get the current balance of a ledger",
        parameters: [
            new OA\Parameter(
                name: "ledgerId",
                description: "Ledger UUID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Ledger balances by currency",
                content: new OA\JsonContent(
                    example: [
                        "USD" => "150.00",
                        "EUR" => "-50.00"
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Ledger not found")
        ]
    )]
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
