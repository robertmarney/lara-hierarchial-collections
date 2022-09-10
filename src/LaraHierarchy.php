<?php

namespace RCM\LaraHierarchy;

use Illuminate\Support\Collection;

class LaraHierarchy
{
    /**
     * Take a flat collection and transform into a hierarchical collection
     *
     * @param  Collection  $collection       - Data to be translated to a hierarchy of nodes.
     * @param  string  $parentIdentifier - default: 'parent_id'
     * @param  string  $relationName     - default: 'children
     * @param  string  $localIdentifier  - default 'id'
     * @return Collection
     */
    public function collectionToHierarchy(Collection $collection, string $parentIdentifier = 'parent_id', string $relationName = 'children', string $localIdentifier = 'id'): Collection
    {
        [$starts, $children] = $collection->partition($parentIdentifier, '=', null);

        return $this->attachChildrenToParent($starts, $children, $parentIdentifier, $relationName, $localIdentifier);
    }

    /**
     * Recursively append child objects to a collection of parents.
     *
     * @param  Collection  $levelItems
     * @param  Collection  $allItems
     * @param  string  $parentIdentifier
     * @param  string  $relationName
     * @param  string  $localIdentifier
     * @return Collection
     */
    private function attachChildrenToParent(Collection $levelItems, Collection $allItems, string $parentIdentifier = 'parent_id', string $relationName = 'children', string $localIdentifier = 'id'): Collection
    {
        return $levelItems->map(function ($item) use ($relationName, $parentIdentifier, $allItems, $localIdentifier) {
            $allChildren = $allItems->where($parentIdentifier, $item->{$localIdentifier});
            $item->setRelation($relationName, $allChildren);
            if (count($allChildren) > 0) {
                $this->attachChildrenToParent($item->{$relationName}, $allItems, $parentIdentifier, $relationName, $localIdentifier);
            }

            return $item;
        });
    }
}
