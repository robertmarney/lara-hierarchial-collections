<?php

use Illuminate\Database\Eloquent\Collection;
use RCM\LaraHierarchy\Hierarchical;
use RCM\LaraHierarchy\Tests\Fixtures\BaseItem;

it('can handle multiple start nodes with no children', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push(new BaseItem(['id' => 2]));

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

    /* Assert */
    $this->assertCount(2, $result);
});

it('can handle single start node with child', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('can handle multiple starts node with children', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push(new BaseItem(['id' => 3]));
    $collection->push($child2 = new BaseItem(['id' => 4, 'parent_id' => 3]));

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

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
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 1]));

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(2, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
    $this->assertEquals($child2->id, $result->first()->children->last()->id);
});

it('can handle 10 levels deep', function () {
    /* Setup */
    $collection = new Collection();
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
    $result = (new Hierarchical($collection))->collection();

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
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));

    /* Transact */
    $result = (new Hierarchical($collection, relationName: 'notChildren'))->collection();

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
    $result = (new Hierarchical($collection, localIdentifier: 'custom_primary_key'))->collection();

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
    $result = (new Hierarchical($collection, parentIdentifier: 'custom_parent_id'))->collection();

    /* Assert */

    $this->assertCount(1, $result);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('is not impacted by sort order', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child4 = new BaseItem(['id' => 5, 'parent_id' => 4]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => 2]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push($child7 = new BaseItem(['id' => 8, 'parent_id' => 6]));
    $collection->push($child3 = new BaseItem(['id' => 4, 'parent_id' => 3]));
    $collection->push($child6 = new BaseItem(['id' => 7, 'parent_id' => 6]));
    $collection->push($child5 = new BaseItem(['id' => 6, 'parent_id' => 5]));

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

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
    $result = (new Hierarchical($collection))->collection();

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('can work with support collections of stdClass', function () {
    $collection = collect();

    $parent = new stdClass();
    $parent->id = 1;
    $parent->parent_id = null;

    $child = new stdClass();
    $child->id = 2;
    $child->parent_id = 1;

    $collection->push($parent);
    $collection->push($child);

    /* Transact */
    $result = (new Hierarchical($collection))->collection();

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
});

it('can be made', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push(new BaseItem(['id' => 2]));

    /* Transact */
    $result = Hierarchical::make($collection)->collection();

    /* Assert */
    $this->assertCount(2, $result);
    $this->assertTrue($result instanceof Collection);
    $this->assertTrue($result->first()->id === 1);
    $this->assertTrue($result->last()->id === 2);
});

it('can serialize deep arrays', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($parent2 = new BaseItem(['id' => 3]));
    $collection->push($child2 = new BaseItem(['id' => 4, 'parent_id' => $parent2->id]));
    $collection->push($child3 = new BaseItem(['id' => 5, 'parent_id' => $parent2->id]));
    $collection->push($grantchild2 = new BaseItem(['id' => 5, 'parent_id' => $child2->id]));

    /* Transact */
    $result = Hierarchical::make($collection)->toArray();

    $this->assertTrue(is_array($result));

    $this->assertEquals([
        'id' => 1,
        'children' => [
            0 => [
                'id' => 2,
                'children' => [],
                'parent_id' => 1,
            ],
        ],
    ], $result[0]);

    $this->assertEquals([
        'id' => 3,
        'children' => [
            0 => [
                'id' => 4,
                'children' => [
                    0 => [
                        'id' => 5,
                        'children' => [],
                        'parent_id' => 4,
                    ],
                ],
                'parent_id' => 3,
            ],
            1 => [
                'id' => 5,
                'children' => [],
                'parent_id' => 3,
            ],
        ],
    ], $result[1]);
});

it('can start at a specific parent', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));
    $collection->push($greatGrandchild = new BaseItem(['id' => 5, 'parent_id' => $grandchild->id]));

    /* Transact */
    $result = (new Hierarchical($collection))->descendantsOf(2);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->parent_id);
    $this->assertEquals($child->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($grandchild->id, $result->first()->children->first()->id);
    $this->assertCount(1, $result->first()->children->first()->children);
    $this->assertEquals($greatGrandchild->id, $result->first()->children->first()->children->first()->id);

});

it('can prepare a list of ancestors', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));
    $collection->push($grandchild2 = new BaseItem(['id' => 5, 'parent_id' => $child2->id]));
    $collection->push($greatGrandchild = new BaseItem(['id' => 6, 'parent_id' => $grandchild->id]));

    /* Transact */
    $result = (new Hierarchical($collection))->ancestorsOf(6);

    /* Assert */
    $this->assertCount(1, $result);
    $this->assertEquals($parent->id, $result->first()->id);
    $this->assertCount(1, $result->first()->children);
    $this->assertEquals($child->id, $result->first()->children->first()->id);
    $this->assertCount(1, $result->first()->children->first()->children);
    $this->assertEquals($grandchild->id, $result->first()->children->first()->children->first()->id);
    $this->assertCount(1, $result->first()->children->first()->children->first()->children);
    $this->assertEquals($greatGrandchild->id, $result->first()->children->first()->children->first()->children->first()->id);
});

it('can test if items are siblings', function () {
    /* Setup */
    $collection = new Collection();
    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));
    $collection->push($grandchild2 = new BaseItem(['id' => 5, 'parent_id' => $child2->id]));
    $collection->push($greatGrandchild = new BaseItem(['id' => 6, 'parent_id' => $grandchild->id]));

    /* Transact */
    $this->assertTrue((new Hierarchical($collection))->is($child)->siblingOf($child2));
    $this->assertTrue((new Hierarchical($collection))->is($child2)->siblingOf($child));
    $this->assertFalse((new Hierarchical($collection))->is($child)->siblingOf($grandchild));
    $this->assertFalse((new Hierarchical($collection))->is($child)->siblingOf($greatGrandchild));
    $this->assertFalse((new Hierarchical($collection))->is($child)->siblingOf($parent));
    $this->assertFalse((new Hierarchical($collection))->is($child)->siblingOf($child));

});

it('can test if items are children of item', function () {

    /* Setup */
    $collection = new Collection();

    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));

    $this->assertTrue((new Hierarchical($collection))->is($child)->childOf($parent));
    $this->assertTrue((new Hierarchical($collection))->is($child2)->childOf($parent));
    $this->assertFalse((new Hierarchical($collection))->is($parent)->childOf($child));
    $this->assertFalse((new Hierarchical($collection))->is($parent)->childOf($child2));
    $this->assertFalse((new Hierarchical($collection))->is($parent)->childOf($grandchild));
    $this->assertFalse((new Hierarchical($collection))->is($parent)->childOf($parent));

});

it('can test if items are ancestors of item', function () {

    /* Setup */
    $collection = new Collection();

    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));

    $this->assertTrue((new Hierarchical($collection))->is($parent)->ancestorOf($child));
    $this->assertTrue((new Hierarchical($collection))->is($parent)->ancestorOf($child2));
    $this->assertTrue((new Hierarchical($collection))->is($parent)->ancestorOf($grandchild));
    $this->assertFalse((new Hierarchical($collection))->is($child)->ancestorOf($parent));
    $this->assertFalse((new Hierarchical($collection))->is($child2)->ancestorOf($parent));
    $this->assertFalse((new Hierarchical($collection))->is($grandchild)->ancestorOf($parent));

});

it('can measure the depth of items', function () {
    /* Setup */
    $collection = new Collection();

    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));

    $this->assertEquals(0, (new Hierarchical($collection))->depthOf($parent));
    $this->assertEquals(1, (new Hierarchical($collection))->depthOf($child));
    $this->assertEquals(1, (new Hierarchical($collection))->depthOf($child2));
    $this->assertEquals(2, (new Hierarchical($collection))->depthOf($grandchild));

});

it('can find the direct siblings of an item', function () {
    /* Setup */
    $collection = new Collection();

    $collection->push($parent = new BaseItem(['id' => 1]));
    $collection->push($child = new BaseItem(['id' => 2, 'parent_id' => $parent->id]));
    $collection->push($child2 = new BaseItem(['id' => 3, 'parent_id' => $parent->id]));
    $collection->push($grandchild = new BaseItem(['id' => 4, 'parent_id' => $child->id]));

    $result = (new Hierarchical($collection))->siblingsOf($child);

    $this->assertCount(1, $result);
    $this->assertEquals($child2->id, $result->first()->id);

    $result = (new Hierarchical($collection))->siblingsOf($child2);

    $this->assertCount(1, $result);
    $this->assertEquals($child->id, $result->first()->id);

    $result = (new Hierarchical($collection))->siblingsOf($parent);
    $this->assertCount(0, $result);

});
