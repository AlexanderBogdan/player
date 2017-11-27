<?php

declare(strict_types=1);

namespace AppBundle\Service;

use Rs\Json\Patch;
use Rs\Json\Patch\FailedTestException;
use Rs\Json\Patch\InvalidOperationException;
use Rs\Json\Patch\InvalidPatchDocumentJsonException;
use Rs\Json\Patch\InvalidTargetDocumentJsonException;
use Rs\Json\Patch\Operations\Replace;

class JsonPatcher
{
    /**
     * @throws InvalidTargetDocumentJsonException
     * @throws InvalidPatchDocumentJsonException
     * @throws InvalidOperationException
     * @throws FailedTestException
     */
    public function patch(string $targetDocument, string $patchDocument): string
    {
        $patch = new Patch($targetDocument, $patchDocument, Replace::APPLY);

        return $patch->apply();
    }
}
