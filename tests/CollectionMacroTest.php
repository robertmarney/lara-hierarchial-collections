<?php

use RCM\LaraHierarchy\Hierarchical;
use RCM\LaraHierarchy\Tests\Fixtures\BaseItem;

it('can be used as macro on collection', function () {
    $collection = collect();
    $collection->push(new BaseItem(['id' => 1]));
    $collection->push(new BaseItem(['id' => 2, 'parent_id' => 1]));
    $collection->push(new BaseItem(['id' => 3, 'parent_id' => 1]));


    $result = $collection->toHierarchical();
    $this->assertTrue($result instanceof Hierarchical);
    $this->assertCount(1, $result->collection());
    $this->assertCount(2, $result->collection()->first()->children);

});
