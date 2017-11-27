<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidPlayerFilterValidation;
use AppBundle\Exception\InvalidPlayerValidation;
use AppBundle\Exception\PlayerNotFoundException;
use AppBundle\Model\PlayerFilter;
use AppBundle\Service\Player as PlayerService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\View\View;
use LogicException;
use Rs\Json\Patch\FailedTestException;
use Rs\Json\Patch\InvalidOperationException;
use Rs\Json\Patch\InvalidPatchDocumentJsonException;
use Rs\Json\Patch\InvalidTargetDocumentJsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @RouteResource("players")
 */
class PlayerController extends FOSRestController
{
    /**
     * @throws InvalidParameterException
     * @throws InvalidPlayerFilterValidation
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function cgetAction(Request $request): View
    {
        $filter = PlayerFilter::createFromRequest($request);

        $players = $this->getPlayerService()->getPlayers($filter);
        $playerCount = $this->getPlayerService()->getPlayerRecordAmount();

        $view = $this->view($players);
        $view->setHeader('X-Total-Records', $playerCount);

        return $view;
    }

    /**
     * @throws NonUniqueResultException
     * @throws PlayerNotFoundException
     */
    public function getAction(string $playerId): View
    {
        $player = $this->getPlayerService()->getPlayer($playerId);

        return $this->view($player);
    }

    /**
     * @throws LogicException
     * @throws InvalidPlayerValidation
     * @throws PlayerNotFoundException
     * @throws NonUniqueResultException
     */
    public function putAction(string $playerId, Request $request): View
    {
        $player = $this->getPlayerService()->getPlayer($playerId);
        $data = (string) $request->getContent();
        $updatedPlayer = $this->getPlayerService()->updatePlayer($player, $data);

        return $this->view($updatedPlayer);
    }

    /**
     * @throws LogicException
     * @throws InvalidTargetDocumentJsonException
     * @throws InvalidPatchDocumentJsonException
     * @throws InvalidOperationException
     * @throws FailedTestException
     * @throws InvalidPlayerValidation
     * @throws PlayerNotFoundException
     * @throws NonUniqueResultException
     */
    public function patchAction(string $playerId, Request $request): View
    {
        $player = $this->getPlayerService()->getPlayer($playerId);
        $data = (string) $request->getContent();
        $updatedPlayer = $this->getPlayerService()->patchPlayer($player, $data);

        return $this->view($updatedPlayer);
    }

    private function getPlayerService(): PlayerService
    {
        return $this->get('app.service.player');
    }
}
