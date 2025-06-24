<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimitListener
{
    public function __construct(private readonly RateLimiterFactory $apiLimiter) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/transactions') &&
            !str_starts_with($request->getPathInfo(), '/ledgers') &&
            !str_starts_with($request->getPathInfo(), '/balances')) {
            return;
        }

        $limiter = $this->apiLimiter->create($request->getClientIp());

        if (!$limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException('Rate limit exceeded');
        }
    }
}