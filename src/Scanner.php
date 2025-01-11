<?php

namespace Collective\Annotations;

use ReflectionClass;
use ReflectionException;

class Scanner
{
    /**
     * The paths to scan for annotations.
     *
     * @var list<class-string>
     */
    protected $scan = [];

    /**
     * Get all of the ReflectionClass instances in the scan array.
     *
     * @return list<ReflectionClass>
     */
    protected function getClassesToScan(): array
    {
        $classes = [];

        foreach ($this->scan as $class) {
            try {
                $classes[] = new ReflectionClass($class);
            } catch (ReflectionException $e) { // @phpstan-ignore catch.neverThrown
                //
            }
        }

        return $classes;
    }

    /**
     * Set the classes to scan.
     *
     * @param list<class-string> $scans
     * @return void
     */
    public function setClassesToScan(array $scans)
    {
        $this->scan = $scans;
    }
}
