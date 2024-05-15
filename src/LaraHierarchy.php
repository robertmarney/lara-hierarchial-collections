<?php

namespace RCM\LaraHierarchy;

use Illuminate\Support\Collection;

class LaraHierarchy
{
    public function __construct()
    {

    }

    /**
     * @deprecated - Use Hierarchical::make($args)->toCollection() instead
     * Take a flat collection of objects and transform into a hierarchical collection
     *
     * @param  Collection  $collection  - Data to be translated to a hierarchy of nodes.
     * @param  string  $parentIdentifier  - default: 'parent_id'
     * @param  string  $relationName  - default: 'children
     * @param  string  $localIdentifier  - default 'id'
     */
    public function collectionToHierarchy(Collection $collection, string $parentIdentifier = 'parent_id', string $relationName = 'children', string $localIdentifier = 'id'): Collection
    {
        return (new Hierarchical($collection, $parentIdentifier, $relationName, $localIdentifier))->collection();
    }
}
