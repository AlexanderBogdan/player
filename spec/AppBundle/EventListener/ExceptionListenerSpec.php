<?php

namespace spec\AppBundle\EventListener;

use AppBundle\EventListener\ExceptionListener;
use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @mixin ExceptionListener
 */
class ExceptionListenerSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith('dev');
    }

    function it_should_not_listen_in_a_development_environment
    (
        GetResponseForExceptionEvent $event
    ) {
        $event->setResponse(Argument::which('getStatusCode', Response::HTTP_BAD_REQUEST))->shouldNotBeCalled();
        $event->setResponse(Argument::type(Response::class))->shouldNotBeCalled();

        $this->onKernelException($event);
    }

    function it_should_listen_for_non_http_exceptions_in_a_production_environment
    (
        GetResponseForExceptionEvent $event,
        Exception $exception
    ) {
        $this->beConstructedWith('prod');

        $event->getException()->willReturn($exception);

        $event->setResponse(Argument::which('getStatusCode', Response::HTTP_BAD_REQUEST))->shouldBeCalled();
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $this->onKernelException($event);
    }

    function it_should_listen_for_http_exceptions_in_a_production_environment
    (
        GetResponseForExceptionEvent $event,
        HttpException $exception
    ) {
        $this->beConstructedWith('prod');

        $event->getException()->willReturn($exception);

        $event->setResponse(Argument::which('getStatusCode', Response::HTTP_BAD_REQUEST))->shouldNotBeCalled();
        $event->setResponse(Argument::type(Response::class))->shouldBeCalled();

        $this->onKernelException($event);
    }
}
