<?php

use Illuminate\Database\Eloquent\Collection;
use RCM\LaraHierarchy\Tests\Fixtures\BaseItem;

beforeEach(function () {
    $this->service = new RCM\LaraHierarchy\LaraHierarchy();
});

it('can handle multiple start nodes with no children', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push(new BaseItem(['id' => 2]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(2, $result);
});

it('can handle single start node with child', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($result->first()->id, $parent->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($result->first()->children->first()->id, $child->id);
});

it('can handle multiple starts node with children', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push(new BaseItem(['id' => 3]));
    $collection->push($child2 = new BaseItem(['id' => 4, 'parent_id' => 3]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(2, $result);
    $this->assertEquals($result->first()->id, $parent->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($result->first()->children->first()->id, $child->id);
    $this->assertCount(1, $result->last()->children);
    $this->assertEquals($result->last()->children->first()->id, $child2->id);
});

it('can handle multiple children', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($result->first()->id, $parent->id);
    $this->assertCount(2, $result->first()->children);
    $this->assertEquals($result->first()->children->first()->id, $child->id);
    $this->assertEquals($result->first()->children->last()->id, $child2->id);
});

it('can handle 6 levels deep', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 2]));
    $collection->push($child3 = new BaseItem(['id' => 4, 'parent_id' => 3]));
    $collection->push($child4 = new BaseItem(['id' => 5, 'parent_id' => 4]));
    $collection->push($child5 = new BaseItem(['id' => 6, 'parent_id' => 5]));
    $collection->push($child6 = new BaseItem(['id' => 7, 'parent_id' => 6]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($result->first()->id, $parent->id);
    $this->assertCount(1, $children = $result->first()->children);
    $this->assertEquals($children->first()->id, $child->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child2->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child3->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child4->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child5->id);
    $this->assertCount(1, $children = $children->first()->children);
    $this->assertEquals($children->first()->id, $child6->id);
});

it('can handle custom relation name', function () {
    /* Setup */
    $collection = new Collection();
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
    $collection = new Collection();
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
    $collection = new Collection();
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'custom_parent_id' => 1]));

    /* Transact */
    $result = $this->service->collectionToHierarchy($collection, parentIdentifier: 'custom_parent_id');

    /* Assert */

    $this->assertCount(1, $result);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});
