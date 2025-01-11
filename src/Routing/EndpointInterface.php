<?php

namespace Collective\Annotations\Routing;

interface EndpointInterface
{
    /**
     * Transform the endpoint into a route definition.
     *
     * @return string
     */
    public function toRouteDefinition(): string;

    /**
     * Get the detail about endpoint that helps to create route definition.
     *
     * @return array
     */
    public function toRouteDefinitionDetail(): array;

    /**
     * Determine if the endpoint has any paths.
     *
     * @return bool
     */
    public function hasPaths(): bool;

    /**
     * Get all the path definitions for an endpoint.
     *
     * @return list<Path|ResourcePath>
     */
    public function getPaths(): array;

    /**
     * Add the given path definition to the endpoint.
     *
     * @param AbstractPath $path
     *
     * @return void
     */
    public function addPath(AbstractPath $path);
}
