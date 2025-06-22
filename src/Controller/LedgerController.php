<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LedgerRequest;
use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/ledgers')]
final class LedgerController extends AbstractController
{
    #[Route('', name: 'create_ledger', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = LedgerRequest::fromArray($data);

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        $ledger = new Ledger();
        $ledger->setBaseCurrency(strtoupper($dto->currency));

        $em->persist($ledger);
        $em->flush();

        return $this->json([
            'id' => $ledger->getId(),
            'currency' => $ledger->getBaseCurrency(),
        ]);
    }
}
