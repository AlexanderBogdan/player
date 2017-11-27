<?php

namespace spec\AppBundle\Manager;

use AppBundle\Entity\Event;
use AppBundle\Entity\Player;
use AppBundle\Event\Events;
use AppBundle\Event\PlayerEvent;
use AppBundle\Manager\PlayerManager;
use AppBundle\Model\PlayerFilter;
use AppBundle\Repository\PlayerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Wimigames\Bundle\EventStreamBundle\EventDispatcher;

class PlayerManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PlayerManager::class);
    }

    function let(
        EntityManager $entityManager,
        Serializer $serializer
    ) {
        $this->beConstructedWith($entityManager, $serializer);
    }

    function it_should_get_a_players_list
    (
        EntityManager $entityManager,
        PlayerFilter $playerFilter,
        PlayerRepository $repository
    ) {
        $entityManager->getRepository(Player::class)->willReturn($repository);
        $repository->findAllByPlayerFilter($playerFilter)->willReturn(
            ['a player', 'another player']
        );

        $this->getPlayers($playerFilter)->shouldReturn(['a player', 'another player']);
    }

    function it_can_update_a_player
    (
        EntityManager $entityManager,
        Player $originalPlayer,
        Player $updatedPlayer,
        Serializer $serializer
    ) {
        $updatedPlayer->getExternalId()->willReturn(Argument::type('string'));
        $updatedPlayer->getCountry()->willReturn(Argument::type('string'));
        $updatedPlayer->getSex()->willReturn(Argument::type('string'));
        $updatedPlayer->getCurrency()->willReturn(Argument::type('string'));
        $updatedPlayer->getJurisdiction()->willReturn(Argument::type('string'));
        $updatedPlayer->isActive()->willReturn('boolean');

        $entityManager->flush()->shouldBeCalled();

        $serializer->serialize($originalPlayer, 'json')->willReturn('a serialized player');

        $this->updatePlayer($originalPlayer, $updatedPlayer)->shouldReturn($originalPlayer);
    }

    function it_can_get_a_player
    (
        EntityManager $entityManager,
        PlayerRepository $repository,
        Player $player
    ) {

        $entityManager->getRepository(Player::class)->willReturn($repository);
        $repository->findPlayer('a player id')->willReturn($player);

        $this->getPlayer('a player id')->shouldBe($player);
    }

    function it_can_get_the_total_amount_of_player_records
    (
        EntityManager $entityManager,
        PlayerRepository $repository
    ) {
        $entityManager->getRepository(Player::class)->willReturn($repository);
        $repository->getPlayerRecordAmount()->willReturn(10);

        $this->getPlayerRecordAmount()->shouldBe(10);
    }
}
