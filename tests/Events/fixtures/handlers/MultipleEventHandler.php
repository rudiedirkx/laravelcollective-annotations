<?php

namespace App\Handlers\Events;

use App\Events\AnotherEvent;
use App\Events\BasicEvent;
use Collective\Annotations\Events\Attributes\Attributes\Hears;

class MultipleEventHandler
{
    #[Hears('BasicEventFired')]
    #[\ReturnTypeWillChange()]
    public function handleBasicEvent(BasicEvent $event)
    {
        // do things
    }

    #[Hears('BasicEventFired')]
    public function handleBasicEventAgain(BasicEvent $event)
    {
        // do things
    }

    #[Hears('AnotherEventFired')]
    public function handleAnotherEvent(AnotherEvent $event)
    {
        // do things
    }

    #[Hears(['BasicEventFired', 'AnotherEventFired'])]
    public function handleBothEventsInOne($event)
    {
        // do things
    }

    #[Hears('BasicEventFired')]
    #[Hears('AnotherEventFired')]
    public function handleBothEventsInTwo($event)
    {
        // do things
    }
}

