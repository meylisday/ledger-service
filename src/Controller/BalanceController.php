<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ledger;
use App\Services\MockCurrencyConverter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/balances')]
final class BalanceController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        private readonly MockCurrencyConverter $converter
    ) {}

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
    public function getBalance(string $ledgerId): JsonResponse
    {
        if (!Uuid::isValid($ledgerId)) {
            throw new BadRequestHttpException('Invalid UUID format');
        }

        $ledger = $this->em->getRepository(Ledger::class)->find($ledgerId);

        if (!$ledger) {
            throw new NotFoundHttpException('Ledger not found');
        }

        $balances = [];

        foreach ($ledger->getTransactions() as $transaction) {
            $currency = $transaction->getCurrency();
            $amount = (float)$transaction->getAmount();
            $type = $transaction->getType();

            $balances[$currency] ??= 0.0;

            if ($type === 'credit') {
                $balances[$currency] += $amount;
            } elseif ($type === 'debit') {
                $balances[$currency] -= $amount;
            }
        }

        $this->logger->info('Balance calculated', [
            'ledgerId' => $ledgerId,
            'balances' => $balances
        ]);

        return $this->json($balances);
    }

    #[Route('/{ledgerId}/convert', name: 'convert_balance', methods: ['GET'])]
    #[OA\Get(
        summary: "Convert total ledger balance to a target currency",
        parameters: [
            new OA\Parameter(
                name: "ledgerId",
                description: "Ledger UUID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            ),
            new OA\Parameter(
                name: "currency",
                description: "Target currency (e.g. USD, EUR)",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", maxLength: 3)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Total converted balance",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "total", type: "number", example: 150.55),
                        new OA\Property(property: "currency", type: "string", example: "USD")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Missing or invalid currency"),
            new OA\Response(response: 404, description: "Ledger not found")
        ]
    )]
    public function convertBalance(string $ledgerId, Request $request): JsonResponse
    {
        $targetCurrency = strtoupper($request->query->get('currency', ''));

        if (empty($targetCurrency)) {
            throw new BadRequestHttpException('Target currency is required');
        }

        $ledger = $this->em->getRepository(Ledger::class)->find($ledgerId);
        if (!$ledger) {
            throw new NotFoundHttpException('Ledger not found');
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

            $balances[$currency] += $type === 'credit' ? $amount : -$amount;
        }

        $total = 0.0;
        foreach ($balances as $currency => $amount) {
            $total += $this->converter->convert($amount, $currency, $targetCurrency);
        }

        $this->logger->info('Balance converted', [
            'ledgerId' => $ledgerId,
            'total' => $total,
            'currency' => $targetCurrency
        ]);

        return $this->json([
            'total' => round($total, 4),
            'currency' => $targetCurrency
        ]);
    }
}
