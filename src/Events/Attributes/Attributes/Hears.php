<?php

namespace Collective\Annotations\Events\Attributes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Hears
{
    /** @var list<string> */
    public array $events;

    /**
     * @param string|list<string> $events
     */
    public function __construct(array|string $events)
    {
        $this->events = is_array($events)? $events: [$events];
    }
}
