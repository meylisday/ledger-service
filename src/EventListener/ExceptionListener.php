<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class ExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof TooManyRequestsHttpException) {
            $response = new JsonResponse([
                'error' => $exception->getMessage() ?: 'Rate limit exceeded'
            ], 429);

            $event->setResponse($response);

            $this->logger->warning('Rate limit exceeded', [
                'path' => $event->getRequest()->getPathInfo(),
                'ip' => $event->getRequest()->getClientIp()
            ]);

            return;
        }

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $this->logger->error('Unhandled exception', [
            'message' => $exception->getMessage(),
            'code' => $statusCode
        ]);

        $response = new JsonResponse([
            'error' => $exception->getMessage()
        ], $statusCode);

        $event->setResponse($response);
    }
}
