<?php

namespace RCM\LaraHierarchy;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Hierarchical implements Arrayable
{
    private object $testItem;

    public function __construct(private Collection $items, private string $parentIdentifier = 'parent_id', private string $relationName = 'children', private string $localIdentifier = 'id')
    {
    }

    public static function make(...$args): Hierarchical
    {
        return new self(...$args);
    }

    /**
     * Returns the hierarchy as an array
     */
    public function toArray(): array
    {
        return $this->collection()->toArray();
    }

    /**
     * Returns a full hierarchy of the items
     */
    public function collection(): Collection
    {
        [$starts, $children] = $this->items->sortBy($this->parentIdentifier)->partition($this->parentIdentifier, '=', null);

        return $this->attachChildrenToParent($starts, $children);
    }

    /**
     * Returns a sparse hierarchy of descendants of the given id
     */
    public function descendantsOf($id): Collection
    {
        $startingValue = $this->items->firstWhere($this->localIdentifier, $id);

        if (! $startingValue) {
            return new Collection();
        }

        return $this->attachChildrenToParent(new Collection([$startingValue]), $this->items);

    }

    /**
     * Returns a sparse hierarchy of ancestors of the given id
     */
    public function ancestorsOf(mixed $itemOrId): Collection
    {
        $id = $this->toId($itemOrId);
        $endingValue = $this->items->firstWhere($this->localIdentifier, $id);

        if (! $endingValue) {
            return new Collection();
        }

        return $this->attachToParent($endingValue);
    }

    /**
     * Returns a collection of all siblings (not including the item itself)
     */
    public function siblingsOf(mixed $itemOrId): Collection
    {
        $item = $this->toItem($itemOrId);

        if (! $item) {
            return new Collection();
        }

        return $this->items
            ->where($this->parentIdentifier, $this->getParentKey($item))
            ->where($this->localIdentifier, '!=', $this->getKey($item))
            ->values();
    }

    /**
     * Initialize the test item for fluent comparison
     */
    public function is(mixed $itemOrId): static
    {
        $this->testItem = $this->toItem($itemOrId);

        return $this;

    }

    /**
     * Test if the test item is a child of the given item
     */
    public function childOf(mixed $id): bool
    {
        if (! $target = $this->toItem($id)) {
            return false;
        }

        return $this->getParentKey($this->testItem) === $this->getKey($target);
    }

    /**
     * Test if the test item is an ancestor of the given item
     */
    public function ancestorOf(mixed $itemOrId): bool
    {
        if (! $target = $this->toItem($itemOrId)) {
            return false;
        }

        return $this
            ->ancestorsOf($target)
            ->flatten()
            ->where($this->localIdentifier, $this->getKey($this->testItem))
            ->isNotEmpty();
    }

    /**
     * Returns the integer depth of the given item in the hierarchy
     */
    public function depthOf(mixed $itemOrId): ?int
    {
        $item = $this->toItem($itemOrId);

        return $this->depth($item);
    }

    /**
     * Find an item by its local identifier
     */
    public function findById(mixed $id): ?object
    {
        return $this->items->firstWhere($this->localIdentifier, $id);
    }

    /**
     * Test if item is a sibling of the given item
     */
    public function siblingOf(mixed $itemOrId): bool
    {
        if (! $target = $this->toItem($itemOrId)) {
            return false;
        }

        if ($this->getKey($target) === $this->getKey($this->testItem)) {
            return false;
        }

        return $this->getParentKey($this->testItem) === $this->getParentKey($target);

    }

    private function toItem(mixed $itemOrId): ?object
    {
        if (is_object($itemOrId)) {
            return $itemOrId;
        }

        return $this->findById($itemOrId);
    }

    private function attachToParent($item): Collection
    {
        $parent = $this->items->firstWhere($this->localIdentifier, $this->getParentKey($item));

        if ($parent) {
            if (method_exists($parent, 'setRelation')) {
                $parent->setRelation($this->relationName, new Collection([$item]));
            } else {
                $parent->{$this->relationName} = new Collection([$item]);
            }

            return $this->attachToParent($parent);
        }

        return new Collection([$item]);

    }

    private function attachChildrenToParent(Collection $levelItems, Collection $allItems): Collection
    {
        return $levelItems->map(function ($item) use ($allItems) {
            [$directChildren, $remainingItems] = $allItems->partition($this->parentIdentifier, '=', $this->getKey($item));

            if (method_exists($item, 'setRelation')) {
                $item->setRelation($this->relationName, $directChildren->values());
            } else {
                $item->{$this->relationName} = $directChildren->values();
            }

            if (! empty($directChildren)) {
                $this->attachChildrenToParent($item->{$this->relationName}, $remainingItems);
            }

            return $item;
        })->values();
    }

    private function depth(mixed $itemOrId, int $depth = 0): int
    {
        $item = $this->toItem($itemOrId);

        if ($this->getParentKey($item) === null) {
            return $depth;
        }

        $parent = $this->items->firstWhere($this->localIdentifier, $this->getParentKey($item));

        return $this->depth($parent, $depth + 1);
    }

    private function getParentKey(object $item): mixed
    {
        return $item->{$this->parentIdentifier};
    }

    private function getKey(object $item): mixed
    {
        return $item->{$this->localIdentifier};
    }

    private function toId(mixed $itemOrId): mixed
    {
        if (is_object($itemOrId)) {
            return $itemOrId->{$this->localIdentifier};
        }

        return $itemOrId;
    }
}
