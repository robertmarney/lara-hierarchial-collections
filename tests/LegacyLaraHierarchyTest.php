<?php

use Illuminate\Database\Eloquent\Collection;
use RCM\LaraHierarchy\Tests\Fixtures\BaseItem;

beforeEach(function () {
    $this->service = new RCM\LaraHierarchy\LaraHierarchy;
});

it('can handle multiple start nodes with no children', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push(new BaseItem(['id' => 2]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(2, $result);
});

it('can handle single start node with child', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('can handle multiple starts node with children', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push(new BaseItem(['id' => 3]));
    $collection->push($child2 = new BaseItem(['id' => 4, 'parent_id' => 3]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(2, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
    $this->assertCount(1, $result->last()->children);
    $this->assertEquals($child2->id, $result->last()->children->first()->id);
});

it('can handle multiple children', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(2, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
    $this->assertEquals($child2->id, $result->first()->children->last()->id);
});

it('can handle 10 levels deep', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 2]));
    $collection->push($child3 = new BaseItem(['id' => 4, 'parent_id' => 3]));
    $collection->push($child4 = new BaseItem(['id' => 5, 'parent_id' => 4]));
    $collection->push($child5 = new BaseItem(['id' => 6, 'parent_id' => 5]));
    $collection->push($child6 = new BaseItem(['id' => 7, 'parent_id' => 6]));
    $collection->push($child7 = new BaseItem(['id' => 8, 'parent_id' => 7]));
    $collection->push($child8 = new BaseItem(['id' => 9, 'parent_id' => 8]));
    $collection->push($child9 = new BaseItem(['id' => 10, 'parent_id' => 9]));
    $collection->push($child10 = new BaseItem(['id' => 11, 'parent_id' => 9]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $children = $result->first()->children);
    $this->assertEquals($child->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child2->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child3->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child4->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child5->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child6->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child7->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child8->id, $children->first()->id);
    $this->assertCount(2, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child9->id);
    $this->assertEquals($children->last()->id, $child10->id);
});

it('can handle custom relation name', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection, relationName: 'notChildren');

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($result->first()->id, $parent->id);
    $this->assertCount(1, $result->first()->notChildren);
    $this->assertEquals($result->first()->notChildren->first()->id, $child->id);
});

it('can handle custom local identifier', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push(new BaseItem(['custom_primary_key' => 1]));
    $collection->push($child = new BaseItem(['custom_primary_key' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection, localIdentifier: 'custom_primary_key');

    /* Assert */

    $this->assertCount(1, $result);
    $this->assertEquals($child->custom_primary_key, $result->first()->children->first()->custom_primary_key);
});

it('can handle custom parent identifier', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'custom_parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection, parentIdentifier: 'custom_parent_id');

    /* Assert */

    $this->assertCount(1, $result);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('is not impacted by sort order', function () {
    /* Setup */
    $collection = new Collection;
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child4 = new BaseItem(['id' => 5, 'parent_id' => 4]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 2]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child7 = new BaseItem(['id' => 8, 'parent_id' => 6]));
    $collection->push($child3 = new BaseItem(['id' => 4, 'parent_id' => 3]));
    $collection->push($child6 = new BaseItem(['id' => 7, 'parent_id' => 6]));
    $collection->push($child5 = new BaseItem(['id' => 6, 'parent_id' => 5]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $children = $result->first()->children);
    $this->assertEquals($child->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child2->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child3->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child4->id, $children->first()->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($child5->id, $children->first()->id);
    $this->assertCount(2, $children = $children->first()->children);
    $this->assertEquals(collect([$child6->id, $child7->id]), $children->sort()->pluck('id'));
});

it('can work with support collections', function () {
    $collection = collect();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('can work with support collections of stdClass', function () {
    $collection = collect();

    $parent = new stdClass;
    $parent->id = 1;
    $parent->parent_id = null;

    $child = new stdClass;
    $child->id = 2;
    $child->parent_id = 1;

    $collection->push($parent);
    $collection->push($child);

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});
