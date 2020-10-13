<?php

namespace App\Contracts\Repositories;

/**
 * Interface PostRepository.
 *
 * @package namespace App\Contracts\Repositories;
 */
interface PostRepository extends BaseRepositoryInterface
{
    public function filter(array $map);
}
