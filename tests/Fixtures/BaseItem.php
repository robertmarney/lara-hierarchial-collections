<?php

namespace RCM\LaraHierarchy\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $custom_primary_key
 */
class BaseItem extends Model
{
    public $fillable = ['id', 'parent_id', 'custom_parent_id', 'custom_primary_key'];
}
