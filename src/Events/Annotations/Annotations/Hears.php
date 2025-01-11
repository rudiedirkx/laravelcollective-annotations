<?php

namespace Collective\Annotations\Events\Annotations\Annotations;

/**
 * @Annotation
 */
class Hears
{
    /**
     * The events the annotation hears.
     *
     * @var list<string>
     */
    public $events;

    /**
     * Create a new annotation instance.
     *
     * @param array{value: string|list<string>} $values
     *
     * @return void
     */
    public function __construct(array $values)
    {
        $this->events = (array) $values['value'];
    }
}
