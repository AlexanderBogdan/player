<?php

declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Player;
use AppBundle\Exception\PlayerNotFoundException;
use AppBundle\Model\PlayerFilter;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PlayerRepository extends EntityRepository
{
    public function findAllByPlayerFilter(PlayerFilter $filter): array
    {
        $alias = 'player';
        $sort = $filter->getSort($alias);
        $order = $filter->getOrder();
        $page = $filter->getOffset();
        $limit = $filter->getLimit();
        $isActive = $filter->isActive();

        $expr = $this->getEntityManager()->getExpressionBuilder();
        $queryBuilder = $this->createQueryBuilder($alias);

        if ('' !== $isActive) {
            $queryBuilder->where($expr->eq($alias.'.active', ':active'));
            $queryBuilder->setParameter('active', $isActive);
        }

        $queryBuilder->orderBy($sort, $order);
        $queryBuilder->setFirstResult($page);
        $queryBuilder->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findMatchingPlayers(Player $player): Collection
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria->where($expr->eq('externalId', $player->getExternalId()));

        return $this->matching($criteria);
    }

    /**
     * @throws NonUniqueResultException
     * @throws PlayerNotFoundException
     */
    public function findPlayer(string $playerId): Player
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $queryBuilder = $this->createQueryBuilder('player')
            ->where($expr->eq('player.id', ':playerId'))
            ->setParameter('playerId', $playerId);

        $player = $queryBuilder->getQuery()->getOneOrNullResult();
        if (null === $player) {
            throw new PlayerNotFoundException();
        }

        return $player;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getPlayerRecordAmount(): int
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $queryBuilder = $this->createQueryBuilder('player')->select($expr->count('player.id'));

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
