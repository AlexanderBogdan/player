<?php

namespace spec\AppBundle\Model;

use AppBundle\Model\PlayerFilter;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

class PlayerFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PlayerFilter::class);
    }

    function let()
    {
        $this->beConstructedWith(1, 1, 10, null);
    }

    function it_has_an_offset_filter()
    {
        $this->beConstructedWith(1, 5, 10, null);
        $this->getOffset()->shouldReturn(40);
    }

    function it_has_a_limit_filter()
    {
        $this->beConstructedWith(1, 5, 10, null);
        $this->getLimit()->shouldReturn(10);
    }

    function it_can_sort_asc_by_a_default_filter()
    {
        $this->beConstructedWith(1, 5, 10, null);
        $this->getSort('player')->shouldReturn('player.id');
        $this->getOrder()->shouldReturn('ASC');
    }

    function it_can_sort_asc_by_filter()
    {
        $this->beConstructedWith(1, 5, 10, 'country');
        $this->getSort('player')->shouldReturn('player.country');
        $this->getOrder()->shouldReturn('ASC');
    }

    function it_can_sort_desc_by_filter()
    {
        $this->beConstructedWith(1, 5, 10, '-country');
        $this->getSort('player')->shouldReturn('player.country');
        $this->getOrder()->shouldReturn('DESC');
    }

    function it_can_create_itself_from_a_request()
    {
        $request = new Request();
        $this->beConstructedThrough('createFromRequest', [$request]);
        $playerFilter = $this::createFromRequest($request);

        $playerFilter->shouldBeAnInstanceOf(PlayerFilter::class);
        $playerFilter->getLimit()->shouldBe(10);
        $playerFilter->getOffset()->shouldBe(0);
        $playerFilter->getOrder()->shouldBe('ASC');
        $playerFilter->isActive()->shouldBe(false);
        $playerFilter->getSort('player')->shouldBe('player.id');
    }
}
