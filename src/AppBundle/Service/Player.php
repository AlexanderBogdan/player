<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Player as PlayerEntity;
use AppBundle\Exception\InvalidPlayerFilterValidation;
use AppBundle\Exception\InvalidPlayerValidation;
use AppBundle\Manager\PlayerManager;
use AppBundle\Model\PlayerFilter;
use JMS\Serializer\Serializer;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class Player
{
    private $playerManager;
    private $jsonPatcher;
    private $recursiveValidator;
    private $serializer;

    /**
     * @param PlayerManager $playerManager
     * @param JsonPatcher $jsonPatcher
     * @param RecursiveValidator $recursiveValidator
     * @param Serializer $serializer
     */
    public function __construct(
        PlayerManager $playerManager,
        JsonPatcher $jsonPatcher,
        RecursiveValidator $recursiveValidator,
        Serializer $serializer
    ) {
        $this->playerManager = $playerManager;
        $this->recursiveValidator = $recursiveValidator;
        $this->serializer = $serializer;
        $this->jsonPatcher = $jsonPatcher;
    }

    public function getPlayers(PlayerFilter $filter): array
    {
        $validator = $this->recursiveValidator->validate($filter);

        if (0 !== $validator->count()) {
            // throw exception
            throw new InvalidPlayerFilterValidation($validator);
        }

        // return the return value
        $return = $this->playerManager->getPlayers($filter);

        return $return;
    }

    public function updatePlayer($originalPlayer, $data)
    {
        $newPlayer = $this
            ->serializer
            ->deserialize(
                $data,
                PlayerEntity::class,
                'json'
            );

        $validator = $this->recursiveValidator->validate($newPlayer);
        if (0 !== $validator->count()) {
            throw new InvalidPlayerValidation($validator);
        }

        return $this->playerManager->updatePlayer($originalPlayer, $newPlayer);
    }

    public function patchPlayer(PlayerEntity $originalPlayer, string $patchDocument): PlayerEntity
    {
        $targetDocument = $this->serializer->serialize($originalPlayer, 'json');
        $patchedDocument = $this->jsonPatcher->patch($targetDocument, $patchDocument);
        $newPlayer = $this->serializer->deserialize($patchedDocument, PlayerEntity::class, 'json');

        $validator = $this
            ->recursiveValidator
            ->validate($newPlayer);

        if (0 !== $validator->count()) {
            throw new InvalidPlayerValidation($validator);
        }

        return $this->playerManager->updatePlayer($originalPlayer, $newPlayer);
    }

    public function getPlayer(string $playerId): PlayerEntity
    {
        return $this
            ->playerManager->getPlayer($playerId);
    }

    public function getPlayerRecordAmount(): int
    {
        return $this->playerManager
            ->getPlayerRecordAmount();
    }
}
