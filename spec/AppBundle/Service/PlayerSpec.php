<?php

namespace spec\AppBundle\Service;

use AppBundle\Entity\Player as PlayerEntity;
use AppBundle\Exception\DoublePlayerException;
use AppBundle\Exception\InvalidPlayerFilterValidation;
use AppBundle\Exception\InvalidPlayerValidation;
use AppBundle\Manager\PlayerManager;
use AppBundle\Model\PlayerFilter;
use AppBundle\Service\JsonPatcher;
use AppBundle\Service\Player;
use ArrayIterator;
use JMS\Serializer\Serializer;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class PlayerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Player::class);
    }

    function let
    (
        PlayerManager $playerManager,
        JsonPatcher $jsonPatcher,
        RecursiveValidator $recursiveValidator,
        Serializer $serializer
    ) {
        $this->beConstructedWith($playerManager, $jsonPatcher, $recursiveValidator, $serializer);
    }

    function it_should_get_an_exception_for_an_invalid_filter
    (
        PlayerFilter $playerFilter,
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        ArrayIterator $iterator
    ) {
        $constraintViolationList->count()->willReturn(1);
        $constraintViolationList->getIterator()->willReturn($iterator);

        $recursiveValidator->validate($playerFilter)->willReturn($constraintViolationList);

        $this->shouldThrow(InvalidPlayerFilterValidation::class)->duringGetPlayers($playerFilter);
    }

    function it_should_get_an_exception_for_an_invalid_player_while_updating_a_player
    (
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        PlayerEntity $originalPlayer,
        PlayerEntity $updatedPlayer,
        Serializer $serializer,
        ArrayIterator $iterator
    ) {
        $serializer->deserialize('invalid player data', PlayerEntity::class, 'json')->willReturn(
            $updatedPlayer
        );

        $constraintViolationList->count()->willReturn(1);
        $constraintViolationList->getIterator()->willReturn($iterator);

        $recursiveValidator->validate($updatedPlayer)->willReturn($constraintViolationList);

        $this->shouldThrow(InvalidPlayerValidation::class)->duringUpdatePlayer(
            $originalPlayer,
            'invalid player data'
        );
    }

    function it_should_get_an_exception_for_an_invalid_player_while_patching_a_player
    (
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        PlayerEntity $originalPlayer,
        PlayerEntity $updatedPlayer,
        Serializer $serializer,
        JsonPatcher $jsonPatcher,
        ArrayIterator $iterator
    ) {
        $constraintViolationList->count()->willReturn(1);
        $constraintViolationList->getIterator()->willReturn($iterator);

        $recursiveValidator->validate($updatedPlayer)->willReturn($constraintViolationList);

        $serializer->serialize($originalPlayer, 'json')->willReturn('target doc');

        $jsonPatcher->patch('target doc', 'patch doc')->willReturn('patched doc');

        $serializer->deserialize('patched doc', PlayerEntity::class, 'json')->willReturn(
            $updatedPlayer
        );

        $this->shouldThrow(InvalidPlayerValidation::class)->duringPatchPlayer(
            $originalPlayer,
            'patch doc'
        );
    }

    function it_should_get_players_for_a_filter
    (
        PlayerFilter $playerFilter,
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        PlayerManager $playerManager
    ) {
        $constraintViolationList->count()->willReturn(0);
        $recursiveValidator->validate($playerFilter)->willReturn($constraintViolationList);

        $playerFilter->getOffset()->willReturn(10);
        $playerFilter->getLimit()->willReturn(100);

        $playerManager->getPlayers($playerFilter)->willReturn(
            ['a player entity', 'another player entity']
        );

        $this->getPlayers($playerFilter)->shouldReturn(
            ['a player entity', 'another player entity']
        );
    }

    function it_should_update_a_player
    (
        PlayerManager $playerManager,
        Serializer $serializer,
        PlayerEntity $originalPlayer,
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        PlayerEntity $updatedPlayer
    ) {
        $constraintViolationList->count()->willReturn(0);
        $recursiveValidator->validate($updatedPlayer)->willReturn($constraintViolationList);

        $serializer->deserialize('a json string', PlayerEntity::class, 'json')->willReturn(
            $updatedPlayer
        );

        $playerManager->updatePlayer($originalPlayer, $updatedPlayer)->willReturn($updatedPlayer);

        $this->updatePlayer($originalPlayer, 'a json string')->shouldReturn($updatedPlayer);
    }

    function it_should_patch_a_player
    (
        PlayerManager $playerManager,
        Serializer $serializer,
        PlayerEntity $originalPlayer,
        PlayerEntity $updatedPlayer,
        RecursiveValidator $recursiveValidator,
        ConstraintViolationList $constraintViolationList,
        JsonPatcher $jsonPatcher
    ) {
        $constraintViolationList->count()->willReturn(0);
        $recursiveValidator->validate($updatedPlayer)->willReturn($constraintViolationList);

        $serializer->serialize($originalPlayer, 'json')->willReturn('target doc');

        $jsonPatcher->patch('target doc', 'patch doc')->willReturn('patched doc');

        $serializer->deserialize('patched doc', PlayerEntity::class, 'json')->willReturn(
            $updatedPlayer
        );

        $playerManager->updatePlayer($originalPlayer, $updatedPlayer)->willReturn($updatedPlayer);

        $this->patchPlayer($originalPlayer, 'patch doc')->shouldReturn($updatedPlayer);
    }

    function it_can_get_a_player(PlayerEntity $player, PlayerManager $playerManager)
    {
        $playerManager->getPlayer('a player id')->willReturn($player);
        $this->getPlayer('a player id')->shouldBe($player);
    }

    function it_can_get_the_total_amount_of_player_records(PlayerManager $playerManager)
    {
        $playerManager->getPlayerRecordAmount()->willReturn(12345);
        $this->getPlayerRecordAmount()->shouldBe(12345);
    }
}
