<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TransactionRequest;
use App\Entity\Ledger;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/transactions')]
final class TransactionController extends AbstractController
{
    #[Route('', name: 'create_transaction', methods: ['POST'])]
    #[OA\Post(
        summary: "Create a new transaction",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: TransactionRequest::class))
        ),
        responses: [
            new OA\Response(response: 200, description: "Transaction successfully recorded"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = TransactionRequest::fromArray($data);

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $ledger = $em->getRepository(Ledger::class)->find($dto->ledgerId);
        if (!$ledger) {
            return $this->json(['error' => 'Ledger not found'], 404);
        }

        if ($em->getRepository(Transaction::class)->findOneBy(['transactionId' => $dto->transactionId])) {
            return $this->json(['error' => 'Transaction ID already used'], 409);
        }

        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setTransactionId($dto->transactionId);
        $transaction->setType($dto->type);
        $transaction->setAmount($dto->amount);
        $transaction->setCurrency($dto->currency);
        $transaction->setCreatedAt(new \DateTimeImmutable());

        $em->persist($transaction);
        $em->flush();

        return $this->json(['status' => 'success']);
    }
}
