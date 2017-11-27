<?php

declare(strict_types=1);

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if ('dev' === $this->environment) {
            return;
        }

        $exception = $event->getException();
        $response = new Response();

        if (!($exception instanceof HttpExceptionInterface)) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $event->setResponse($response);
    }
}
