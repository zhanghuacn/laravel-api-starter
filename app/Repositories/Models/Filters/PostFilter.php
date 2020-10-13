<?php

namespace App\Repositories\Models\Filters;

use EloquentFilter\ModelFilter;

class PostFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function title($title)
    {
        return $this->where('title', 'LIKE', "$title%");
    }
}
