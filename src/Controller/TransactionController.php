<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TransactionRequest;
use App\Entity\Ledger;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Throwable;

#[Route('/transactions')]
final class TransactionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws Throwable
     */
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
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON body'], 400);
        }

        $dto = TransactionRequest::fromArray($data);

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        if (!Uuid::isValid($dto->ledgerId) || !Uuid::isValid($dto->transactionId)) {
            throw new BadRequestHttpException('Invalid UUID format');
        }

        $ledger = $this->em->getRepository(Ledger::class)->find($dto->ledgerId);
        if (!$ledger) {
            throw new NotFoundHttpException('Ledger not found');
        }

        if ($this->em->getRepository(Transaction::class)->findOneBy(['transactionId' => $dto->transactionId])) {
            throw new ConflictHttpException('Transaction ID already used');
        }

        $transaction = new Transaction();
        $transaction->setLedger($ledger);
        $transaction->setTransactionId($dto->transactionId);
        $transaction->setType($dto->type);
        $transaction->setAmount($dto->amount);
        $transaction->setCurrency($dto->currency);
        $transaction->setCreatedAt(new \DateTimeImmutable());

        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $this->em->persist($transaction);
            $this->em->flush();
            $conn->commit();

            $this->logger->info('Transaction created successfully', [
                'transactionId' => $dto->transactionId,
                'amount' => $dto->amount,
                'currency' => $dto->currency
            ]);
        } catch (\Throwable $e) {
            $conn->rollBack();
            $this->logger->error('Transaction creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $this->json(['status' => 'success']);
    }
}
