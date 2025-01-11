<?php

namespace Collective\Annotations\Routing;

use Illuminate\Support\Collection;

/**
 * @extends Collection<array-key, EndpointInterface>
 */
class EndpointCollection extends Collection
{
    /**
     * Get all the paths for the given endpoint collection.
     *
     * @return list<Path|ResourcePath>
     */
    public function getAllPaths(): array
    {
        $paths = [];

        foreach ($this as $endpoint) {
            foreach ($endpoint->getPaths() as $path) {
                $paths[] = $path;
            }
        }

        return $paths;
    }
}
