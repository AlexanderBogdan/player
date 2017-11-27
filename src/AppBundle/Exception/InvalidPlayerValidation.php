<?php

declare(strict_types=1);

namespace AppBundle\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidPlayerValidation extends HttpException
{
    public function __construct(ConstraintViolationListInterface $validator)
    {
        $message = 'Player validation failed.';

        /** @var ConstraintViolationInterface $item */
        foreach ($validator as $item) {
            $message .= sprintf(' %s: %s', $item->getPropertyPath(), $item->getMessage());
        }

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }
}
