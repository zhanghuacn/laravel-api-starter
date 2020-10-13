<?php


namespace App\Contracts\Repositories;


use Prettus\Repository\Contracts\RepositoryInterface;

interface BaseRepositoryInterface extends RepositoryInterface
{
    public function filter(array $map);
}
