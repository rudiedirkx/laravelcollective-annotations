<?php

namespace Collective\Annotations\Database\Eloquent\Attributes;

use Collective\Annotations\Database\Eloquent\Attributes\Attributes\Bind;
use Collective\Annotations\Database\ScanStrategyInterface;
use ReflectionAttribute;
use ReflectionClass;

class AttributeStrategy implements ScanStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function support(ReflectionClass $class): bool
    {
        return count($class->getAttributes(Bind::class)) > 0;
    }

    /**
     * @inheritDoc
     */
    public function getBindings(ReflectionClass $class): array
    {
        return array_map(
            fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
            $class->getAttributes(Bind::class),
        );
    }
}
