<?php

namespace spec\AppBundle\Service;

use AppBundle\Service\JsonPatcher;
use PhpSpec\ObjectBehavior;

class JsonPatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JsonPatcher::class);
    }

    function it_should_patch_valid_documents()
    {
        $target = '{"id":"80917aac-4aa0-11e7-8340-0242ac130003","externalId":"2365475sdT","country":"BE","gender":"M","currency":"EUR","jurisdiction":"BE","isActive":true}';
        $patch = '[ { "op":"replace", "path":"/isActive", "value": false } ]';

        $patched = '{"id":"80917aac-4aa0-11e7-8340-0242ac130003","externalId":"2365475sdT","country":"BE","gender":"M","currency":"EUR","jurisdiction":"BE","isActive":false}';

        $this->patch($target, $patch)->shouldReturn($patched);
    }
}
