<?php

declare(strict_types=1);

namespace AppBundle\Manager;

use AppBundle\Entity\Player;
use AppBundle\Exception\PlayerNotFoundException;
use AppBundle\Model\PlayerFilter;
use AppBundle\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JMS\Serializer\Serializer;

class PlayerManager
{
    private $entityManager;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        Serializer $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function getPlayers(PlayerFilter $filter): array
    {
        return $this->getRepository()->findAllByPlayerFilter($filter);
    }

    public function updatePlayer(Player $originalPlayer, Player $newPlayer): Player
    {
        $originalPlayer->setExternalId($newPlayer->getExternalId());
        $originalPlayer->setCountry($newPlayer->getCountry());
        $originalPlayer->setCurrency($newPlayer->getCurrency());
        $originalPlayer->setJurisdiction($newPlayer->getJurisdiction());
        $originalPlayer->setActive($newPlayer->isActive());

        if (null !== $newPlayer->getSex()) {
            $originalPlayer->setSex($newPlayer->getSex());
        }

        $this->entityManager->flush();

        return $originalPlayer;
    }

    public function createPlayer(Player $player): Player
    {
        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    /**
     * @throws NonUniqueResultException
     * @throws PlayerNotFoundException
     */
    public function getPlayer(string $playerId): Player
    {
        return $this->getRepository()->findPlayer($playerId);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getPlayerRecordAmount(): int
    {
        return $this->getRepository()->getPlayerRecordAmount();
    }

    private function getRepository(): PlayerRepository
    {
        return $this->entityManager->getRepository(Player::class);
    }
}
