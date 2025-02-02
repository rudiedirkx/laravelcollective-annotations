<?php

namespace App\Http\Controllers;

use Collective\Annotations\Routing\Attributes\Attributes\Any;

#[\AllowDynamicProperties]
class AnyController
{
    #[Any(path: "my-any-route")]
    #[\ReturnTypeWillChange]
    public function anyAnnotations()
    {
    }
}
