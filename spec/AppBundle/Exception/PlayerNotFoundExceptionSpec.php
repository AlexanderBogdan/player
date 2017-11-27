<?php

namespace spec\AppBundle\Exception;

use AppBundle\Exception\PlayerNotFoundException;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlayerNotFoundExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PlayerNotFoundException::class);
    }

    function it_is_an_http_exception_extension()
    {
        $this->shouldHaveType(HttpException::class);
    }

    function it_should_have_a_message()
    {
        $this->beConstructedWith();
        $this->getMessage()->shouldBe('Player not found.');
    }
}
