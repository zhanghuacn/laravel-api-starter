<?php


namespace App\Repositories\Criteria;


use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;

/**
 * Class FilterCriteria
 * @package namespace App\Criteria;
 */
class FilterCriteria implements CriteriaInterface
{
    protected $map;

    /**
     * IsCommonCriteria constructor.
     * @param             $map
     */
    public function __construct($map)
    {
        $this->map = $map;
    }

    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->filter($this->map);
    }
}
