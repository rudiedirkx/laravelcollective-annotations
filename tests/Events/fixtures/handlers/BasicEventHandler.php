<?php

namespace App\Handlers\Events;

use App\Events\BasicEvent;
use Collective\Annotations\Events\Attributes\Attributes\Hears;

class BasicEventHandler
{
    #[Hears(['BasicEventFired'])]
    #[\ReturnTypeWillChange()]
    public function handle(BasicEvent $event)
    {
        // do things
    }
}

