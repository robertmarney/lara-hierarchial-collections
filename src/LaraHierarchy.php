<?php

namespace RCM\LaraHierarchy;

use Illuminate\Support\Collection;

class LaraHierarchy
{
    /**
     * @deprecated - Use Hierarchical::make($args)->toCollection() instead
     * Take a flat collection of objects and transform into a hierarchical collection
     *
     * @param  Collection<int, object>  $collection  - Data to be translated to a hierarchy of nodes.
     * @param  string  $parentIdentifier  - default: 'parent_id'
     * @param  string  $relationName  - default: 'children
     * @param  string  $localIdentifier  - default 'id'
     * @return Collection<int, object>
     */
    public function collectionToHierarchy(Collection $collection, string $parentIdentifier = 'parent_id', string $relationName = 'children', string $localIdentifier = 'id'): Collection
    {
        return (new Hierarchical($collection, $parentIdentifier, $relationName, $localIdentifier))->collection();
    }
}
