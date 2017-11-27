<?php

declare(strict_types=1);

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlayerNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Player not found.');
    }
}
