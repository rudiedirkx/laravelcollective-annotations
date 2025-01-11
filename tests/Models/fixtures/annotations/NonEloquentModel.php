<?php

namespace App;

use Collective\Annotations\Database\Eloquent\Attributes\Attributes\Bind;

/**
 * @Bind("systems")
 */
#[Bind('systems')]
#[\AllowDynamicProperties]
class NonEloquentModel
{
}
