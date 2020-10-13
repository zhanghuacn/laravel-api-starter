<?php

namespace App\Repositories\Models;

use App\Repositories\Models\Filters\PostFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PostResource.
 *
 * @package namespace App\Models;
 */
class Post extends Model implements Transformable
{
    use TransformableTrait, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];

    /**
     * 匹配查询
     * @return string|null
     */
    public function modelFilter()
    {
        return $this->provideFilter(PostFilter::class);
    }

}
