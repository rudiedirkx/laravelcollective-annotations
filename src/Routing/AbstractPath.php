<?php

namespace Collective\Annotations\Routing;

abstract class AbstractPath
{
    /**
     * The HTTP verb the route responds to.
     *
     * @var string
     */
    public $verb;

    /**
     * The domain the route responds to.
     *
     * @var ?string
     */
    public $domain;

    /**
     * The path / URI the route responds to.
     *
     * @var string
     */
    public $path;

    /**
     * The path's middleware.
     *
     * @var list<string>
     */
    public $middleware = [];

    /**
     * The path's "where" clauses.
     *
     * @var array<string, string>
     */
    public $where = [];
}
