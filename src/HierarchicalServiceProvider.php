<?php

namespace RCM\LaraHierarchy;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class HierarchicalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Collection::macro('toHierarchical', function (string $parentIdentifier = 'parent_id', string $relationName = 'children', string $localIdentifier = 'id'): Hierarchical {
            return new Hierarchical($this, $parentIdentifier, $relationName, $localIdentifier);
        });
    }
}
