<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Criteria\FilterCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\PostRepository;
use App\Repositories\Models\Post;
use App\Repositories\Validators\PostValidator;

/**
 * Class PostRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class PostRepositoryEloquent extends BaseRepository implements PostRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Post::class;
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {

        return PostValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 自动设置过滤
     *
     * @param array $map
     * @return PostRepositoryEloquent
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function filter(array $map)
    {
        $this->popCriteria(FilterCriteria::class);

        return $this->pushCriteria(new FilterCriteria($map));
    }

}
