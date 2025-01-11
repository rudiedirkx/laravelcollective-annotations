<?php

namespace Collective\Annotations\Routing;

use RuntimeException;

class ResourcePath extends AbstractPath
{
    /**
     * The controller method of the resource path.
     *
     * @var string
     */
    public $method;

    /**
     * Create a new Resource Path instance.
     *
     * @param string $method
     *
     * @return void
     */
    public function __construct(string $method)
    {
        $this->method = $method;
        $this->verb = $this->getVerb($method);
    }

    /**
     * Get the verb for the given resource method.
     *
     * @param string $method
     *
     * @return string
     */
    protected function getVerb(string $method): string
    {
        switch ($method) {
            case 'index':
            case 'create':
            case 'show':
            case 'edit':
                return 'get';

            case 'store':
                return 'post';

            case 'update':
                return 'put';

            case 'destroy':
                return 'delete';
        }

        throw new RuntimeException(sprintf("Unknown verb '%s' in ResourcePath", $method));
    }
}
