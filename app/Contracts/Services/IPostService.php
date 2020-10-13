<?php


namespace App\Contracts\Services;


use App\Repositories\Models\Post;
use Illuminate\Http\Request;

interface IPostService
{
    /**
     * 新增
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request);

    public function show($id);
}
