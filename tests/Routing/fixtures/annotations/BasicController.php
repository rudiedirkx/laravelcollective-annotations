<?php

namespace App\Http\Controllers;

use Collective\Annotations\Routing\Attributes\Attributes\Controller;
use Collective\Annotations\Routing\Attributes\Attributes\Middleware;
use Collective\Annotations\Routing\Attributes\Attributes\Put;
use Collective\Annotations\Routing\Attributes\Attributes\Resource;
use Collective\Annotations\Routing\Attributes\Attributes\Where;

#[Resource(name: 'foobar/photos', only: ['index', 'update'], names: ['index' => 'index.name'])]
#[Controller(domain: '{id}.account.com')]
#[Middleware(name: 'FooMiddleware')]
#[Middleware(name: 'BarMiddleware')]
#[Middleware(name: 'BoomMiddleware', options: ['only' => ['index']])]
#[Where(['id'=> 'regex'])]
#[\AllowDynamicProperties]
class BasicController
{
    /**
     * @return Response
     */
    #[Middleware(name: 'BazMiddleware')]
    #[\ReturnTypeWillChange]
    public function index()
    {
    }

    /**
     * @return Response
     */
    public function update($id)
    {
    }

    /**
     * Bla
     */
    #[Put(path: '/more/{id}', after: 'log')]
    #[Middleware(name: 'QuxMiddleware')]
    public function doMore($id)
    {
    }
}
